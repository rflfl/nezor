<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { PhWallet, PhPlus, PhX, PhCurrencyCircleDollar, PhTrendUp, PhTrendDown, PhFunnel } from '@phosphor-icons/vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useApi } from '@/composables/useApi';

const api = useApi();

const cashRegister = ref<any>(null);
const showOpenModal = ref(false);
const showCloseModal = ref(false);
const entries = ref([]);

const form = useForm({
  opening_amount: 0,
  closing_amount: 0,
  closing_note: '',
});

onMounted(() => {
  fetchOpenCashRegister();
});

async function fetchOpenCashRegister() {
  try {
    cashRegister.value = await api.get('/cash-registers/open');
    if (cashRegister.value?.id) {
      const data = await api.get(`/cash-registers/${cashRegister.value.id}`);
      entries.value = data.daily_service_entries || [];
    }
  } catch (e) {
    console.error('Erro ao buscar caixa:', e);
  }
}

async function openCashRegister() {
  try {
    await api.post('/cash-registers', { opening_amount: form.opening_amount });
    showOpenModal.value = false;
    form.reset();
    await fetchOpenCashRegister();
  } catch (e: any) {
    if (e.response?.data?.errors) {
      form.setError(e.response.data.errors);
    }
    console.error('Erro ao abrir caixa:', e);
  }
}

async function closeCashRegister() {
  try {
    await api.put(`/cash-registers/${cashRegister.value.id}`, {
      closing_amount: form.closing_amount,
      closing_note: form.closing_note,
    });
    showCloseModal.value = false;
    form.reset();
    cashRegister.value = null;
    entries.value = [];
  } catch (e: any) {
    if (e.response?.data?.errors) {
      form.setError(e.response.data.errors);
    }
    console.error('Erro ao fechar caixa:', e);
  }
}

function formatCurrency(value: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0);
}

const expectedAmount = computed(() => parseFloat(cashRegister.value?.expected_amount || 0));
const totalExpected = computed(() => parseFloat(cashRegister.value?.opening_amount || 0) + expectedAmount.value);

const entriesByMethod = computed(() => {
  const map: Record<string, number> = {};
  for (const entry of entries.value) {
    const method = entry.payment_method || 'outros';
    map[method] = (map[method] || 0) + parseFloat(entry.gross_amount);
  }
  return map;
});
</script>

<template>
  <AppLayout title="Caixa">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 class="text-2xl font-bold text-text dark:text-white">Caixa</h2>
          <p class="text-muted">Gerencie o caixa do dia</p>
        </div>
        <div v-if="cashRegister?.status === 'open'" class="flex gap-2">
          <button @click="showCloseModal = true" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
            <PhX class="w-5 h-5" />
            Fechar Caixa
          </button>
        </div>
      </div>

      <!-- Open Cash Register -->
      <div v-if="!cashRegister" class="bg-white dark:bg-zinc-900 rounded-2xl p-8 border border-border/50 text-center">
        <PhWallet class="w-16 h-16 mx-auto mb-4 text-muted opacity-50" />
        <h3 class="text-xl font-semibold text-text dark:text-white mb-2">Caixa fechado</h3>
        <p class="text-muted mb-6">Abra o caixa para começar o dia</p>
        <button @click="showOpenModal = true" class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors">
          <PhPlus class="w-5 h-5" />
          Abrir Caixa
        </button>
      </div>

      <!-- Open Cash Register Info -->
      <div v-else class="space-y-4">
        <div class="grid md:grid-cols-3 gap-4">
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
            <p class="text-sm text-muted mb-1">Abertura</p>
            <p class="text-2xl font-bold text-text dark:text-white">{{ formatCurrency(cashRegister.opening_amount) }}</p>
          </div>
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
            <p class="text-sm text-muted mb-1">Recebido</p>
            <p class="text-2xl font-bold text-green-600">{{ formatCurrency(cashRegister.expected_amount) }}</p>
          </div>
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
            <p class="text-sm text-muted mb-1">Total Esperado</p>
            <p class="text-2xl font-bold text-teal-600">{{ formatCurrency(totalExpected) }}</p>
          </div>
        </div>

        <!-- Status Badge -->
        <div class="flex items-center gap-2">
          <span class="w-3 h-3 rounded-full bg-green-600 animate-pulse"></span>
          <span class="text-sm text-muted">Caixa aberto desde {{ cashRegister.open_time }}</span>
        </div>

        <!-- Entries -->
        <div v-if="entries.length > 0" class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
          <h3 class="text-lg font-semibold text-text dark:text-white mb-4">Lançamentos no Caixa</h3>
          <div class="space-y-3">
            <div v-for="entry in entries" :key="entry.id" class="flex items-center justify-between py-2 border-b border-border/30 last:border-0">
              <div>
                <p class="font-medium text-text dark:text-white">{{ entry.customer?.name }}</p>
                <p class="text-xs text-muted">{{ entry.service?.name }} - {{ entry.professional?.name }}</p>
              </div>
              <span class="font-semibold text-teal-600">{{ formatCurrency(entry.gross_amount) }}</span>
            </div>
          </div>
        </div>

        <!-- Summary by method -->
        <div v-if="Object.keys(entriesByMethod).length > 0" class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
          <h3 class="text-lg font-semibold text-text dark:text-white mb-4">Resumo por Forma de Pagamento</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div v-for="(amount, method) in entriesByMethod" :key="method" class="p-4 bg-secondary/30 rounded-xl text-center">
              <p class="text-xs text-muted uppercase">{{ method }}</p>
              <p class="font-semibold text-text dark:text-white">{{ formatCurrency(amount) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Open Modal -->
      <div v-if="showOpenModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showOpenModal = false"></div>
        <div class="relative w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-xl animate-scale-in">
          <h3 class="text-xl font-semibold text-text dark:text-white mb-6">Abrir Caixa</h3>
          <form @submit.prevent="openCashRegister" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Valor Inicial (R$)</label>
              <input v-model="form.opening_amount" type="number" step="0.01" min="0" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
            </div>
            <div v-if="form.errors.opening_amount" class="text-sm text-red-600">{{ form.errors.opening_amount }}</div>
            <div class="flex justify-end gap-3 pt-4">
              <button type="button" @click="showOpenModal = false" class="px-4 py-2 text-muted hover:text-text">Cancelar</button>
              <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-xl hover:bg-teal-700">Abrir</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Close Modal -->
      <div v-if="showCloseModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showCloseModal = false"></div>
        <div class="relative w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-xl animate-scale-in">
          <h3 class="text-xl font-semibold text-text dark:text-white mb-6">Fechar Caixa</h3>
          <div class="mb-4 p-4 bg-secondary/30 rounded-xl space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-muted">Abertura:</span>
              <span class="font-semibold">{{ formatCurrency(cashRegister?.opening_amount) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-muted">Recebido:</span>
              <span class="font-semibold">{{ formatCurrency(cashRegister?.expected_amount) }}</span>
            </div>
            <div class="flex justify-between text-sm border-t border-border/30 pt-2">
              <span class="text-muted">Total Esperado:</span>
              <span class="font-semibold">{{ formatCurrency(totalExpected) }}</span>
            </div>
          </div>
          <form @submit.prevent="closeCashRegister" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Valor Contado (R$)</label>
              <input v-model="form.closing_amount" type="number" step="0.01" min="0" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
            </div>
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Observações</label>
              <textarea v-model="form.closing_note" rows="3" class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none"></textarea>
            </div>
            <div v-if="form.errors.closing_amount || form.errors.status" class="text-sm text-red-600">
              {{ form.errors.closing_amount || form.errors.status }}
            </div>
            <div class="flex justify-end gap-3 pt-4">
              <button type="button" @click="showCloseModal = false" class="px-4 py-2 text-muted hover:text-text">Cancelar</button>
              <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700">Fechar Caixa</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
