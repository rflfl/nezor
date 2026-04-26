<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { PhChartLine, PhCalendar, PhCurrencyCircleDollar, PhUsers, PhScissors, PhWallet, PhFunnel } from '@phosphor-icons/vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useApi, localDateStr } from '@/composables/useApi';

const api = useApi();

const startDate = ref('');
const endDate = ref('');
const report = ref<any>(null);
const loading = ref(false);

onMounted(() => {
  const today = new Date();
  const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
  startDate.value = localDateStr(firstDay);
  endDate.value = localDateStr(today);
  fetchReport();
});

async function fetchReport() {
  loading.value = true;
  try {
    // Reutiliza o dashboard API com os parametros de data, ou podemos criar um endpoint dedicado
    // Por enquanto vamos usar o dashboard que ja retorna mes atual
    const data = await api.get('/dashboard');
    report.value = data;
  } catch (e) {
    console.error('Erro ao carregar relatório:', e);
  } finally {
    loading.value = false;
  }
}

function formatCurrency(value: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0);
}

const periodLabel = computed(() => {
  if (!startDate.value || !endDate.value) return '';
  const start = new Date(startDate.value).toLocaleDateString('pt-BR');
  const end = new Date(endDate.value).toLocaleDateString('pt-BR');
  return `${start} - ${end}`;
});
</script>

<template>
  <AppLayout title="Relatórios">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 class="text-2xl font-bold text-text dark:text-white">Relatórios Financeiros</h2>
          <p class="text-muted">Acompanhe o desempenho do salão</p>
        </div>
      </div>

      <!-- Date Filter -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl p-4 border border-border/50">
        <div class="flex flex-col sm:flex-row gap-4 items-end">
          <div class="flex-1">
            <label class="block text-sm text-muted mb-1">Data Inicial</label>
            <input v-model="startDate" type="date" class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 outline-none" />
          </div>
          <div class="flex-1">
            <label class="block text-sm text-muted mb-1">Data Final</label>
            <input v-model="endDate" type="date" class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 outline-none" />
          </div>
          <button @click="fetchReport" class="px-6 py-2 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors">
            Filtrar
          </button>
        </div>
      </div>

      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="w-8 h-8 border-4 border-teal-600 border-t-transparent rounded-full animate-spin"></div>
      </div>

      <div v-else-if="report" class="space-y-6">
        <!-- Period Info -->
        <p class="text-sm text-muted">Período: {{ periodLabel }}</p>

        <!-- Month Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                <PhCurrencyCircleDollar class="w-5 h-5 text-success" />
              </div>
              <span class="text-sm text-muted">Faturamento</span>
            </div>
            <p class="text-2xl font-bold text-text dark:text-white">{{ formatCurrency(report.month?.revenue) }}</p>
          </div>
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-10 h-10 rounded-lg bg-danger/10 flex items-center justify-center">
                <PhWallet class="w-5 h-5 text-danger" />
              </div>
              <span class="text-sm text-muted">Comissões</span>
            </div>
            <p class="text-2xl font-bold text-text dark:text-white">{{ formatCurrency(report.month?.commission) }}</p>
          </div>
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                <PhChartLine class="w-5 h-5 text-primary" />
              </div>
              <span class="text-sm text-muted">Parte do Salão</span>
            </div>
            <p class="text-2xl font-bold text-text dark:text-white">{{ formatCurrency(report.month?.salon) }}</p>
          </div>
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center">
                <PhScissors class="w-5 h-5 text-accent" />
              </div>
              <span class="text-sm text-muted">Atendimentos</span>
            </div>
            <p class="text-2xl font-bold text-text dark:text-white">{{ report.month?.services || 0 }}</p>
          </div>
        </div>

        <!-- Top Services -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
          <h3 class="text-lg font-semibold text-text dark:text-white mb-4">Serviços Mais Realizados</h3>
          <div v-if="report.top_services?.length" class="space-y-3">
            <div v-for="(service, idx) in report.top_services" :key="idx" class="flex items-center justify-between p-4 bg-secondary/30 rounded-xl">
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-teal-600/10 text-teal-600 flex items-center justify-center text-sm font-bold">{{ idx + 1 }}</span>
                <span class="font-medium text-text dark:text-white">{{ service.name }}</span>
              </div>
              <div class="flex items-center gap-4 text-sm">
                <span class="text-muted">{{ service.count }}x</span>
                <span class="font-semibold text-teal-600">{{ formatCurrency(service.total) }}</span>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8 text-muted">
            <p>Nenhum dado disponível</p>
          </div>
        </div>

        <!-- Top Professionals -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
          <h3 class="text-lg font-semibold text-text dark:text-white mb-4">Comissões por Profissional</h3>
          <div v-if="report.top_professionals?.length" class="space-y-3">
            <div v-for="(prof, idx) in report.top_professionals" :key="idx" class="flex items-center justify-between p-4 bg-secondary/30 rounded-xl">
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-accent/10 text-accent flex items-center justify-center text-sm font-bold">{{ idx + 1 }}</span>
                <span class="font-medium text-text dark:text-white">{{ prof.name }}</span>
              </div>
              <span class="font-semibold text-danger">{{ formatCurrency(prof.commission) }}</span>
            </div>
          </div>
          <div v-else class="text-center py-8 text-muted">
            <p>Nenhum dado disponível</p>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
            <div class="flex items-center gap-3">
              <PhUsers class="w-5 h-5 text-muted" />
              <span class="text-muted">Total de Clientes</span>
            </div>
            <p class="text-2xl font-bold text-text dark:text-white mt-2">{{ report.stats?.total_customers || 0 }}</p>
          </div>
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
            <div class="flex items-center gap-3">
              <PhUsers class="w-5 h-5 text-muted" />
              <span class="text-muted">Profissionais Ativos</span>
            </div>
            <p class="text-2xl font-bold text-text dark:text-white mt-2">{{ report.stats?.total_professionals || 0 }}</p>
          </div>
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
            <div class="flex items-center gap-3">
              <PhScissors class="w-5 h-5 text-muted" />
              <span class="text-muted">Serviços Ativos</span>
            </div>
            <p class="text-2xl font-bold text-text dark:text-white mt-2">{{ report.stats?.total_services || 0 }}</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
