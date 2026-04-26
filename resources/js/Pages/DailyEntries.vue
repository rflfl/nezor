<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { PhClipboardText, PhPlus, PhX, PhPencil, PhTrash, PhCurrencyCircleDollar, PhUser, PhScissors } from '@phosphor-icons/vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useApi, localDateStr } from '@/composables/useApi';

const api = useApi();

const entries = ref([]);
const customers = ref([]);
const professionals = ref([]);
const services = ref([]);
const cashRegister = ref<any>(null);
const showModal = ref(false);
const editingEntry = ref(null);
const selectedDate = ref(localDateStr());

const form = useForm({
  customer_id: '',
  professional_id: '',
  service_id: '',
  service_date: selectedDate.value,
  gross_amount: 0,
  professional_percentage: 40,
  payment_status: 'pending',
  payment_method: 'cash',
  notes: '',
});

const paymentStatusLabels: Record<string, string> = {
  pending: 'Pendente',
  paid: 'Pago',
};

const paymentMethodLabels: Record<string, string> = {
  cash: 'Dinheiro',
  pix: 'PIX',
  card: 'Cartão',
  mixed: 'Misto',
};

onMounted(() => {
  fetchEntries();
  fetchRelatedData();
  fetchCashRegister();
});

async function fetchEntries() {
  try {
    const data = await api.get(`/daily-service-entries?date=${selectedDate.value}`);
    entries.value = data.data || [];
  } catch (e) {
    console.error('Erro ao buscar lancamentos:', e);
  }
}

async function fetchRelatedData() {
  try {
    const [c, p, s] = await Promise.all([
      api.get('/customers?active=true'),
      api.get('/professionals?active=true'),
      api.get('/services?active=true'),
    ]);
    customers.value = c.data || [];
    professionals.value = p.data || [];
    services.value = s.data || [];
  } catch (e) {
    console.error('Erro ao carregar dados:', e);
  }
}

async function fetchCashRegister() {
  try {
    cashRegister.value = await api.get('/cash-registers/open');
  } catch (e) {
    console.error('Erro ao buscar caixa:', e);
  }
}

function openModal(entry = null) {
  if (entry) {
    editingEntry.value = entry;
    form.customer_id = entry.customer_id;
    form.professional_id = entry.professional_id;
    form.service_id = entry.service_id;
    form.service_date = entry.service_date;
    form.gross_amount = entry.gross_amount;
    form.professional_percentage = entry.professional_percentage;
    form.payment_status = entry.payment_status;
    form.payment_method = entry.payment_method || 'cash';
    form.notes = entry.notes || '';
  } else {
    editingEntry.value = null;
    form.reset();
    form.service_date = selectedDate.value;
    form.professional_percentage = 40;
    form.payment_status = 'pending';
    form.payment_method = 'cash';
  }
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editingEntry.value = null;
  form.reset();
}

async function saveEntry() {
  try {
    if (editingEntry.value) {
      await api.put(`/daily-service-entries/${editingEntry.value.id}`, form.data());
    } else {
      await api.post('/daily-service-entries', form.data());
    }
    closeModal();
    fetchEntries();
    fetchCashRegister();
  } catch (e: any) {
    if (e.response?.data?.errors) {
      form.setError(e.response.data.errors);
    }
    console.error('Erro ao salvar lancamento:', e);
  }
}

async function deleteEntry(entry: any) {
  if (confirm(`Tem certeza que deseja excluir o atendimento de ${entry.customer?.name}?`)) {
    try {
      await api.delete(`/daily-service-entries/${entry.id}`);
      fetchEntries();
      fetchCashRegister();
    } catch (e) {
      console.error('Erro ao excluir lancamento:', e);
    }
  }
}

function changeDate(days: number) {
  const date = new Date(selectedDate.value);
  date.setDate(date.getDate() + days);
  selectedDate.value = localDateStr(date);
  fetchEntries();
}

function formatDate(dateStr: string) {
  return new Date(dateStr).toLocaleDateString('pt-BR', { weekday: 'long', day: 'numeric', month: 'long' });
}

function formatCurrency(value: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0);
}

const dayTotals = computed(() => {
  const gross = entries.value.reduce((sum, e) => sum + parseFloat(e.gross_amount), 0);
  const commission = entries.value.reduce((sum, e) => sum + parseFloat(e.professional_amount), 0);
  const salon = entries.value.reduce((sum, e) => sum + parseFloat(e.salon_amount), 0);
  return { gross, commission, salon };
});
</script>

<template>
  <AppLayout title="Lançamentos">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 class="text-2xl font-bold text-text dark:text-white">Lançamentos do Dia</h2>
          <p class="text-muted">Registre os atendimentos realizados</p>
        </div>
        <button @click="openModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors">
          <PhPlus class="w-5 h-5" />
          Novo Lançamento
        </button>
      </div>

      <!-- Date Navigation -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl p-4 border border-border/50">
        <div class="flex items-center justify-between">
          <button @click="changeDate(-1)" class="p-2 text-muted hover:text-teal-600 transition-colors">
            &larr;
          </button>
          <div class="text-center">
            <h3 class="text-lg font-semibold text-text dark:text-white">{{ formatDate(selectedDate) }}</h3>
          </div>
          <button @click="changeDate(1)" class="p-2 text-muted hover:text-teal-600 transition-colors">
            &rarr;
          </button>
        </div>
      </div>

      <!-- Totals -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
          <p class="text-sm text-muted mb-1">Faturamento Bruto</p>
          <p class="text-2xl font-bold text-text dark:text-white">{{ formatCurrency(dayTotals.gross) }}</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
          <p class="text-sm text-muted mb-1">Comissões</p>
          <p class="text-2xl font-bold text-danger">{{ formatCurrency(dayTotals.commission) }}</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
          <p class="text-sm text-muted mb-1">Parte do Salão</p>
          <p class="text-2xl font-bold text-primary">{{ formatCurrency(dayTotals.salon) }}</p>
        </div>
      </div>

      <!-- Cash Register Alert -->
      <div v-if="!cashRegister" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-4 flex items-center gap-3">
        <span class="w-3 h-3 rounded-full bg-yellow-600"></span>
        <p class="text-sm text-yellow-800 dark:text-yellow-200">
          Caixa fechado. Os lançamentos marcados como "Pago" exigem caixa aberto.
        </p>
      </div>
      <div v-else class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-4 flex items-center gap-3">
        <span class="w-3 h-3 rounded-full bg-green-600 animate-pulse"></span>
        <p class="text-sm text-green-800 dark:text-green-200">
          Caixa aberto. Recebimentos serão vinculados automaticamente.
        </p>
      </div>

      <!-- Entries List -->
      <div class="space-y-3">
        <div
          v-for="entry in entries"
          :key="entry.id"
          class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50"
        >
          <div class="flex items-start justify-between">
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-full bg-teal-600/20 flex items-center justify-center">
                <span class="text-teal-600 font-semibold">{{ entry.customer?.name.charAt(0) }}</span>
              </div>
              <div>
                <h3 class="font-semibold text-text dark:text-white">{{ entry.customer?.name }}</h3>
                <div class="flex flex-wrap items-center gap-3 mt-1 text-sm text-muted">
                  <span class="flex items-center gap-1">
                    <PhScissors class="w-3 h-3" />
                    {{ entry.service?.name }}
                  </span>
                  <span class="flex items-center gap-1">
                    <PhUser class="w-3 h-3" />
                    {{ entry.professional?.name }}
                  </span>
                  <span :class="entry.payment_status === 'paid' ? 'bg-green-600/10 text-green-600' : 'bg-yellow-600/10 text-yellow-600'" class="px-2 py-0.5 rounded-full text-xs font-medium">
                    {{ paymentStatusLabels[entry.payment_status] }}
                  </span>
                  <span v-if="entry.payment_method" class="text-xs bg-secondary/50 px-2 py-0.5 rounded-full">
                    {{ paymentMethodLabels[entry.payment_method] }}
                  </span>
                </div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span class="font-semibold text-teal-600">{{ formatCurrency(entry.gross_amount) }}</span>
              <button @click="openModal(entry)" class="p-2 text-muted hover:text-teal-600 transition-colors">
                <PhPencil class="w-4 h-4" />
              </button>
              <button @click="deleteEntry(entry)" class="p-2 text-muted hover:text-red-600 transition-colors">
                <PhTrash class="w-4 h-4" />
              </button>
            </div>
          </div>
          <div class="mt-3 pt-3 border-t border-border/30 flex gap-4 text-xs text-muted">
            <span>Profissional: {{ formatCurrency(entry.professional_amount) }}</span>
            <span>Salão: {{ formatCurrency(entry.salon_amount) }}</span>
            <span v-if="entry.notes">Obs: {{ entry.notes }}</span>
          </div>
        </div>

        <div v-if="entries.length === 0" class="text-center py-12 text-muted">
          <PhClipboardText class="w-12 h-12 mx-auto mb-4 opacity-50" />
          <p>Nenhum lançamento para esta data</p>
        </div>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="closeModal"></div>
        <div class="relative w-full max-w-md bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-xl animate-scale-in max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-text dark:text-white">
              {{ editingEntry ? 'Editar Lançamento' : 'Novo Lançamento' }}
            </h3>
            <button @click="closeModal" class="p-2 text-muted hover:text-text">
              <PhX class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="saveEntry" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Cliente *</label>
              <select v-model="form.customer_id" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none">
                <option value="">Selecione...</option>
                <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Profissional *</label>
              <select v-model="form.professional_id" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none">
                <option value="">Selecione...</option>
                <option v-for="p in professionals" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Serviço *</label>
              <select v-model="form.service_id" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none">
                <option value="">Selecione...</option>
                <option v-for="s in services" :key="s.id" :value="s.id">{{ s.name }} - {{ formatCurrency(s.price) }}</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text dark:text-white mb-2">Data *</label>
                <input v-model="form.service_date" type="date" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
              </div>
              <div>
                <label class="block text-sm font-medium text-text dark:text-white mb-2">Valor (R$) *</label>
                <input v-model="form.gross_amount" type="number" step="0.01" min="0" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">% Comissão Profissional *</label>
              <input v-model="form.professional_percentage" type="number" step="0.01" min="0" max="100" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text dark:text-white mb-2">Status Pagamento *</label>
                <select v-model="form.payment_status" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none">
                  <option value="pending">Pendente</option>
                  <option value="paid">Pago</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-text dark:text-white mb-2">Forma de Pagamento</label>
                <select v-model="form.payment_method" class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none">
                  <option value="cash">Dinheiro</option>
                  <option value="pix">PIX</option>
                  <option value="card">Cartão</option>
                  <option value="mixed">Misto</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Observações</label>
              <textarea v-model="form.notes" rows="2" class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none"></textarea>
            </div>
            <div v-if="form.errors" class="text-sm text-red-600 space-y-1">
              <p v-for="(error, key) in form.errors" :key="key">{{ Array.isArray(error) ? error[0] : error }}</p>
            </div>

            <div class="flex justify-end gap-3 pt-4">
              <button type="button" @click="closeModal" class="px-4 py-2 text-muted hover:text-text transition-colors">
                Cancelar
              </button>
              <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors">
                {{ editingEntry ? 'Salvar' : 'Criar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
