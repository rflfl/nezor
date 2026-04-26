<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue';
import { Link } from '@inertiajs/vue3';
import { PhChartLine, PhUser, PhScissors, PhCalendar, PhTrendUp, PhTrendDown, PhWallet, PhFunnel, PhGear, PhSignOut, PhList, PhX, PhBell, PhCurrencyDollar } from '@phosphor-icons/vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useApi } from '@/composables/useApi';

const api = useApi();

const dashboard = ref<any>(null);
const loading = ref(true);

onMounted(async () => {
  try {
    dashboard.value = await api.get('/dashboard');
    loading.value = false;
    await nextTick();
    if (typeof window !== 'undefined' && (window as any).ApexCharts) {
      renderCharts();
    }
  } catch (e) {
    console.error('Erro ao carregar dashboard:', e);
    loading.value = false;
  }
});

const stats = computed(() => {
  if (!dashboard.value) return [];
  return [
    {
      title: 'Faturamento Hoje',
      value: formatCurrency(dashboard.value.day?.revenue || 0),
      icon: PhChartLine,
    },
    {
      title: 'Clientes Hoje',
      value: String(dashboard.value.day?.customers || 0),
      icon: PhUser,
    },
    {
      title: 'Serviços Hoje',
      value: String(dashboard.value.day?.services || 0),
      icon: PhScissors,
    },
    {
      title: 'Comissão Dia',
      value: formatCurrency(dashboard.value.day?.commission || 0),
      icon: PhWallet,
    },
  ];
});

const monthStats = computed(() => {
  if (!dashboard.value) return { revenue: '-', profit: '-', expenses: '-', commission: '-' };
  const revenue = dashboard.value.month?.revenue || 0;
  const commission = dashboard.value.month?.commission || 0;
  const salon = dashboard.value.month?.salon || 0;
  return {
    revenue: formatCurrency(revenue),
    profit: formatCurrency(salon),
    expenses: '-',
    commission: formatCurrency(commission),
  };
});

function formatCurrency(value: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0);
}

function renderCharts() {
  const ApexCharts = (window as any).ApexCharts;
  if (!ApexCharts || !dashboard.value) return;

  const last7 = dashboard.value.last_7_days || { labels: [], revenue: [], commission: [] };

  const revenueChart = document.getElementById('revenue-chart');
  if (revenueChart) {
    new ApexCharts(revenueChart, {
      chart: { type: 'area', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif', background: 'transparent' },
      series: [{ name: 'Faturamento', data: last7.revenue }],
      colors: ['#0d9488'],
      fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.1, stops: [0, 100] } },
      stroke: { curve: 'smooth', width: 3 },
      dataLabels: { enabled: false },
      xaxis: { categories: last7.labels, labels: { style: { colors: '#7A7974' } }, axisBorder: { show: false }, axisTicks: { show: false } },
      yaxis: { labels: { style: { colors: '#7A7974' }, formatter: (val: number) => 'R$' + val.toLocaleString() } },
      grid: { borderColor: '#D4D1CA', strokeDashArray: 4 },
      tooltip: { y: { formatter: (val: number) => 'R$ ' + val.toLocaleString('pt-BR') } },
    }).render();
  }

  const servicesChart = document.getElementById('services-chart');
  if (servicesChart) {
    const topServices = dashboard.value.top_services || [];
    new ApexCharts(servicesChart, {
      chart: { type: 'donut', height: 280, fontFamily: 'Inter, sans-serif' },
      series: topServices.length ? topServices.map((s: any) => s.count) : [1],
      labels: topServices.length ? topServices.map((s: any) => s.name) : ['Nenhum'],
      colors: ['#01696F', '#D19900', '#437A22', '#A12C7B', '#964219'],
      legend: { position: 'bottom', labels: { colors: '#7A7974' } },
      plotOptions: { pie: { donut: { size: '70%', labels: { show: true, total: { show: true, label: 'Total', formatter: () => String(dashboard.value.month?.services || 0) + ' serviços' } } } } },
      dataLabels: { enabled: false },
      stroke: { width: 0 },
    }).render();
  }

  const weekChart = document.getElementById('week-chart');
  if (weekChart) {
    new ApexCharts(weekChart, {
      chart: { type: 'bar', height: 200, toolbar: { show: false }, fontFamily: 'Inter, sans-serif', background: 'transparent' },
      series: [{ name: 'Faturamento', data: last7.revenue }],
      colors: ['#0d9488'],
      plotOptions: { bar: { borderRadius: 8, columnWidth: '60%' } },
      dataLabels: { enabled: false },
      xaxis: { categories: last7.labels, labels: { style: { colors: '#7A7974' } }, axisBorder: { show: false } },
      yaxis: { labels: { style: { colors: '#7A7974' }, formatter: (val: number) => 'R$' + val } },
      grid: { borderColor: '#D4D1CA', strokeDashArray: 4 },
    }).render();
  }
}
</script>

<template>
  <AppLayout title="Dashboard">
    <!-- Header -->
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-text dark:text-white">
          Dashboard
        </h2>
        <div class="flex items-center gap-2">
          <span class="text-sm text-muted">Olá,</span>
          <span class="font-medium text-text dark:text-white">{{ $page.props.auth.user?.name }}</span>
        </div>
      </div>
    </template>

    <div v-if="loading" class="flex items-center justify-center py-20">
      <div class="w-8 h-8 border-4 border-teal-600 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else-if="dashboard" class="space-y-8">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          v-for="(stat, index) in stats"
          :key="index"
          class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50 shadow-sm hover:shadow-md transition-shadow"
        >
          <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
              <component :is="stat.icon" class="w-6 h-6 text-primary" />
            </div>
          </div>
          <p class="text-sm text-muted mb-1">{{ stat.title }}</p>
          <p class="text-2xl font-bold text-text dark:text-white">{{ stat.value }}</p>
        </div>
      </div>

      <!-- Cash Register Alert -->
      <div v-if="dashboard.open_cash_register" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-4 flex items-center gap-3">
        <span class="w-3 h-3 rounded-full bg-green-600 animate-pulse"></span>
        <p class="text-sm text-green-800 dark:text-green-200">
          Caixa aberto com fundo de {{ formatCurrency(dashboard.open_cash_register.opening_amount) }} — Esperado: {{ formatCurrency(dashboard.open_cash_register.expected_amount) }}
        </p>
      </div>
      <div v-else class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-4 flex items-center gap-3">
        <span class="w-3 h-3 rounded-full bg-yellow-600"></span>
        <p class="text-sm text-yellow-800 dark:text-yellow-200">
          Nenhum caixa aberto. <Link href="/caixa" class="underline font-medium">Abrir caixa agora</Link>
        </p>
      </div>

      <!-- Charts Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-text dark:text-white">Faturamento Últimos 7 Dias</h3>
          </div>
          <div id="revenue-chart"></div>
        </div>

        <!-- Services Chart -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
          <h3 class="text-lg font-semibold text-text dark:text-white mb-6">Serviços mais realizados</h3>
          <div id="services-chart"></div>
        </div>
      </div>

      <!-- Bottom Section -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Week Summary -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
          <h3 class="text-lg font-semibold text-text dark:text-white mb-6">Resumo da Semana</h3>
          <div id="week-chart"></div>
        </div>

        <!-- Month Summary -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
          <h3 class="text-lg font-semibold text-text dark:text-white mb-6">Resumo do Mês</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-success/10 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-success/20 flex items-center justify-center">
                  <PhCurrencyDollar class="w-5 h-5 text-success" />
                </div>
                <span class="text-muted">Receita</span>
              </div>
              <span class="font-semibold text-success">{{ monthStats.revenue }}</span>
            </div>
            <div class="flex items-center justify-between p-4 bg-primary/10 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary/20 flex items-center justify-center">
                  <PhTrendUp class="w-5 h-5 text-primary" />
                </div>
                <span class="text-muted">Lucro (Salão)</span>
              </div>
              <span class="font-semibold text-primary">{{ monthStats.profit }}</span>
            </div>
            <div class="flex items-center justify-between p-4 bg-warning/10 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-warning/20 flex items-center justify-center">
                  <PhFunnel class="w-5 h-5 text-warning" />
                </div>
                <span class="text-muted">Despesas</span>
              </div>
              <span class="font-semibold text-warning">{{ monthStats.expenses }}</span>
            </div>
            <div class="flex items-center justify-between p-4 bg-danger/10 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-danger/20 flex items-center justify-center">
                  <PhWallet class="w-5 h-5 text-danger" />
                </div>
                <span class="text-muted">Comissão</span>
              </div>
              <span class="font-semibold text-danger">{{ monthStats.commission }}</span>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
          <h3 class="text-lg font-semibold text-text dark:text-white mb-6">Ações Rápidas</h3>
          <div class="space-y-3">
            <Link href="/agenda" class="flex items-center gap-3 p-4 rounded-xl border border-border/50 hover:border-primary hover:bg-primary/5 transition-all group">
              <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary group-hover:scale-110 transition-all">
                <PhCalendar class="w-5 h-5 text-primary" />
              </div>
              <div class="flex-1">
                <p class="font-medium text-text dark:text-white">Agenda</p>
                <p class="text-sm text-muted">Ver agenda do dia</p>
              </div>
            </Link>
            <Link href="/clientes" class="flex items-center gap-3 p-4 rounded-xl border border-border/50 hover:border-primary hover:bg-primary/5 transition-all group">
              <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center group-hover:bg-accent group-hover:scale-110 transition-all">
                <PhUser class="w-5 h-5 text-accent" />
              </div>
              <div class="flex-1">
                <p class="font-medium text-text dark:text-white">Clientes</p>
                <p class="text-sm text-muted">Cadastrar cliente</p>
              </div>
            </Link>
            <Link href="/caixa" class="flex items-center gap-3 p-4 rounded-xl border border-border/50 hover:border-primary hover:bg-primary/5 transition-all group">
              <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center group-hover:bg-success group-hover:scale-110 transition-all">
                <PhWallet class="w-5 h-5 text-success" />
              </div>
              <div class="flex-1">
                <p class="font-medium text-text dark:text-white">Caixa</p>
                <p class="text-sm text-muted">Abrir/fechar caixa</p>
              </div>
            </Link>
            <Link href="/servicos" class="flex items-center gap-3 p-4 rounded-xl border border-border/50 hover:border-primary hover:bg-primary/5 transition-all group">
              <div class="w-10 h-10 rounded-lg bg-danger/10 flex items-center justify-center group-hover:bg-danger group-hover:scale-110 transition-all">
                <PhScissors class="w-5 h-5 text-danger" />
              </div>
              <div class="flex-1">
                <p class="font-medium text-text dark:text-white">Serviços</p>
                <p class="text-sm text-muted">Gerenciar serviços</p>
              </div>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
