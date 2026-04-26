<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { PhScissors, PhPlus, PhMagnifyingGlass, PhX, PhPencil, PhTrash, PhClock, PhCurrencyCircleDollar } from '@phosphor-icons/vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useApi } from '@/composables/useApi';

const api = useApi();

const services = ref([]);
const categories = ref([]);
const search = ref('');
const showModal = ref(false);
const editingService = ref(null);

const form = useForm({
  name: '',
  category: '',
  duration_minutes: 60,
  price: 0,
  professional_percentage: 40,
  salon_percentage: 60,
  active: true,
});

onMounted(() => {
  fetchServices();
  fetchCategories();
});

async function fetchServices() {
  try {
    const data = await api.get(`/services?search=${search.value}`);
    services.value = data.data || [];
  } catch (e) {
    console.error('Erro ao buscar servicos:', e);
  }
}

async function fetchCategories() {
  try {
    categories.value = await api.get('/services/categories');
  } catch (e) {
    console.error('Erro ao buscar categorias:', e);
  }
}

function openModal(service = null) {
  if (service) {
    editingService.value = service;
    form.name = service.name;
    form.category = service.category || '';
    form.duration_minutes = service.duration_minutes;
    form.price = service.price;
    form.professional_percentage = service.professional_percentage;
    form.salon_percentage = service.salon_percentage;
    form.active = service.active;
  } else {
    editingService.value = null;
    form.reset();
    form.professional_percentage = 40;
    form.salon_percentage = 60;
  }
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editingService.value = null;
  form.reset();
}

function saveService() {
  if (editingService.value) {
    form.put(`/api/services/${editingService.value.id}`, {
      onSuccess: () => {
        closeModal();
        fetchServices();
      }
    });
  } else {
    form.post('/api/services', {
      onSuccess: () => {
        closeModal();
        fetchServices();
      }
    });
  }
}

function deleteService(service: any) {
  if (confirm(`Tem certeza que deseja excluir ${service.name}?`)) {
    form.delete(`/api/services/${service.id}`, {
      onSuccess: () => fetchServices()
    });
  }
}

function formatCurrency(value: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
}
</script>

<template>
  <AppLayout title="Serviços">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 class="text-2xl font-bold text-text dark:text-white">Serviços</h2>
          <p class="text-muted">Gerencie os serviços do seu salão</p>
        </div>
        <button @click="openModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors">
          <PhPlus class="w-5 h-5" />
          Novo Serviço
        </button>
      </div>

      <!-- Services Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="service in services"
          :key="service.id"
          class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50 hover:border-teal-600/50 transition-all"
        >
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="font-semibold text-text dark:text-white">{{ service.name }}</h3>
              <span v-if="service.category" class="text-xs text-muted">{{ service.category }}</span>
            </div>
            <span :class="service.active ? 'bg-green-600/10 text-green-600' : 'bg-red-600/10 text-red-600'" class="px-2 py-1 rounded-full text-xs font-medium">
              {{ service.active ? 'Ativo' : 'Inativo' }}
            </span>
          </div>
          
          <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between">
              <span class="text-muted flex items-center gap-1">
                <PhCurrencyCircleDollar class="w-4 h-4" />
                Valor
              </span>
              <span class="font-semibold text-teal-600">{{ formatCurrency(service.price) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-muted flex items-center gap-1">
                <PhClock class="w-4 h-4" />
                Duração
              </span>
              <span>{{ service.duration_minutes }} min</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-muted">Profissional</span>
              <span>{{ service.professional_percentage }}%</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-muted">Salão</span>
              <span>{{ service.salon_percentage }}%</span>
            </div>
          </div>

          <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-border/50">
            <button @click="openModal(service)" class="p-2 text-muted hover:text-teal-600 transition-colors">
              <PhPencil class="w-5 h-5" />
            </button>
            <button @click="deleteService(service)" class="p-2 text-muted hover:text-red-600 transition-colors">
              <PhTrash class="w-5 h-5" />
            </button>
          </div>
        </div>

        <div v-if="services.length === 0" class="col-span-full text-center py-12 text-muted">
          <PhScissors class="w-12 h-12 mx-auto mb-4 opacity-50" />
          <p>Nenhum serviço encontrado</p>
          <button @click="openModal()" class="mt-4 text-teal-600 hover:underline">
            Cadastrar primeiro serviço
          </button>
        </div>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="closeModal"></div>
        <div class="relative w-full max-w-md bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-xl animate-scale-in">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-text dark:text-white">
              {{ editingService ? 'Editar Serviço' : 'Novo Serviço' }}
            </h3>
            <button @click="closeModal" class="p-2 text-muted hover:text-text">
              <PhX class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="saveService" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Nome *</label>
              <input v-model="form.name" type="text" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
            </div>
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Categoria</label>
              <input v-model="form.category" type="text" list="categories" class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
              <datalist id="categories">
                <option v-for="cat in categories" :key="cat" :value="cat" />
              </datalist>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text dark:text-white mb-2">Duração (min) *</label>
                <input v-model="form.duration_minutes" type="number" required min="5" class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
              </div>
              <div>
                <label class="block text-sm font-medium text-text dark:text-white mb-2">Valor (R$) *</label>
                <input v-model="form.price" type="number" required step="0.01" min="0" class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text dark:text-white mb-2">% Profissional *</label>
                <input v-model="form.professional_percentage" type="number" required step="0.01" min="0" max="100" class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
              </div>
              <div>
                <label class="block text-sm font-medium text-text dark:text-white mb-2">% Salão *</label>
                <input v-model="form.salon_percentage" type="number" required step="0.01" min="0" max="100" class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
              </div>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.active" class="w-4 h-4 rounded border-border text-teal-600" />
              <span class="text-sm text-muted">Serviço ativo</span>
            </label>

            <div class="flex justify-end gap-3 pt-4">
              <button type="button" @click="closeModal" class="px-4 py-2 text-muted hover:text-text transition-colors">
                Cancelar
              </button>
              <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors">
                {{ editingService ? 'Salvar' : 'Criar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>