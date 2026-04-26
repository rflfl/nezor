<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { PhUsers, PhPlus, PhMagnifyingGlass, PhX, PhPencil, PhTrash, PhPhone, PhEnvelope, PhPercent } from '@phosphor-icons/vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useApi } from '@/composables/useApi';
import { useMask } from '@/composables/useMask';

const api = useApi();
const { maskPhone } = useMask();

const professionals = ref([]);
const search = ref('');
const showModal = ref(false);
const editingProfessional = ref(null);

const form = useForm({
  name: '',
  phone: '',
  email: '',
  default_commission_percentage: 40,
  active: true,
});

const statusOptions = [
  { value: 'all', label: 'Todos' },
  { value: 'true', label: 'Ativos' },
  { value: 'false', label: 'Inativos' },
];
const statusFilter = ref('all');

onMounted(() => {
  fetchProfessionals();
});

async function fetchProfessionals() {
  try {
    const data = await api.get(`/professionals?search=${search.value}&active=${statusFilter.value === 'all' ? '' : statusFilter.value}`);
    professionals.value = data.data || [];
  } catch (e) {
    console.error('Erro ao buscar profissionais:', e);
  }
}

function openModal(professional = null) {
  if (professional) {
    editingProfessional.value = professional;
    form.name = professional.name;
    form.phone = professional.phone || '';
    form.email = professional.email || '';
    form.default_commission_percentage = professional.default_commission_percentage || 40;
    form.active = professional.active;
  } else {
    editingProfessional.value = null;
    form.reset();
    form.default_commission_percentage = 40;
    form.active = true;
  }
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editingProfessional.value = null;
  form.reset();
}

async function saveProfessional() {
  try {
    if (editingProfessional.value) {
      await api.put(`/professionals/${editingProfessional.value.id}`, form.data());
    } else {
      await api.post('/professionals', form.data());
    }
    closeModal();
    fetchProfessionals();
  } catch (e: any) {
    if (e.response?.data?.errors) {
      form.setError(e.response.data.errors);
    }
    console.error('Erro ao salvar profissional:', e);
  }
}

async function deleteProfessional(professional: any) {
  if (confirm(`Tem certeza que deseja excluir ${professional.name}?`)) {
    try {
      await api.delete(`/professionals/${professional.id}`);
      fetchProfessionals();
    } catch (e) {
      console.error('Erro ao excluir profissional:', e);
    }
  }
}
</script>

<template>
  <AppLayout title="Profissionais">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 class="text-2xl font-bold text-text dark:text-white">Profissionais</h2>
          <p class="text-muted">Gerencie os profissionais do seu salão</p>
        </div>
        <button @click="openModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors">
          <PhPlus class="w-5 h-5" />
          Novo Profissional
        </button>
      </div>

      <!-- Filters -->
      <div class="flex flex-col sm:flex-row gap-4">
        <div class="relative flex-1">
          <PhMagnifyingGlass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted" />
          <input
            v-model="search"
            @input="fetchProfessionals"
            type="text"
            placeholder="Buscar por nome..."
            class="w-full pl-10 pr-4 py-2 bg-white dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all outline-none"
          />
        </div>
        <select
          v-model="statusFilter"
          @change="fetchProfessionals"
          class="px-4 py-2 bg-white dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none"
        >
          <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
        </select>
      </div>

      <!-- Professionals List -->
      <div class="grid gap-4">
        <div
          v-for="professional in professionals"
          :key="professional.id"
          class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50 hover:border-teal-600/50 transition-all"
        >
          <div class="flex items-start justify-between">
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-full bg-teal-600/20 flex items-center justify-center">
                <span class="text-teal-600 font-semibold">{{ professional.name.charAt(0) }}</span>
              </div>
              <div>
                <h3 class="font-semibold text-text dark:text-white">{{ professional.name }}</h3>
                <div class="flex items-center gap-4 mt-1 text-sm text-muted">
                  <span v-if="professional.phone" class="flex items-center gap-1">
                    <PhPhone class="w-4 h-4" />
                    {{ professional.phone }}
                  </span>
                  <span v-if="professional.email" class="flex items-center gap-1">
                    <PhEnvelope class="w-4 h-4" />
                    {{ professional.email }}
                  </span>
                  <span class="flex items-center gap-1">
                    <PhPercent class="w-4 h-4" />
                    {{ professional.default_commission_percentage }}% padrão
                  </span>
                </div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span :class="professional.active ? 'bg-green-600/10 text-green-600' : 'bg-red-600/10 text-red-600'" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ professional.active ? 'Ativo' : 'Inativo' }}
              </span>
              <button @click="openModal(professional)" class="p-2 text-muted hover:text-teal-600 transition-colors">
                <PhPencil class="w-5 h-5" />
              </button>
              <button @click="deleteProfessional(professional)" class="p-2 text-muted hover:text-red-600 transition-colors">
                <PhTrash class="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>

        <div v-if="professionals.length === 0" class="text-center py-12 text-muted">
          <PhUsers class="w-12 h-12 mx-auto mb-4 opacity-50" />
          <p>Nenhum profissional encontrado</p>
          <button @click="openModal()" class="mt-4 text-teal-600 hover:underline">
            Cadastrar primeiro profissional
          </button>
        </div>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="closeModal"></div>
        <div class="relative w-full max-w-md bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-xl animate-scale-in">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-text dark:text-white">
              {{ editingProfessional ? 'Editar Profissional' : 'Novo Profissional' }}
            </h3>
            <button @click="closeModal" class="p-2 text-muted hover:text-text">
              <PhX class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="saveProfessional" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Nome *</label>
              <input v-model="form.name" type="text" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
            </div>
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Telefone</label>
              <input
                :value="form.phone"
                @input="e => form.phone = maskPhone(e.target.value)"
                type="tel"
                placeholder="(00) 00000-0000"
                maxlength="15"
                class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Email</label>
              <input v-model="form.email" type="email" class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
            </div>
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Comissão Padrão (%)</label>
              <input v-model="form.default_commission_percentage" type="number" min="0" max="100" step="0.01" class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.active" class="w-4 h-4 rounded border-border text-teal-600" />
              <span class="text-sm text-muted">Profissional ativo</span>
            </label>
            <div v-if="form.errors" class="text-sm text-red-600 space-y-1">
              <p v-for="(error, key) in form.errors" :key="key">{{ Array.isArray(error) ? error[0] : error }}</p>
            </div>

            <div class="flex justify-end gap-3 pt-4">
              <button type="button" @click="closeModal" class="px-4 py-2 text-muted hover:text-text transition-colors">
                Cancelar
              </button>
              <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors">
                {{ editingProfessional ? 'Salvar' : 'Criar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
