<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { PhFlower, PhHouse, PhCalendar, PhUsers, PhScissors, PhWallet, PhChartLine, PhGear, PhSignOut, PhList, PhX, PhBell, PhClipboardText, PhUserCircle, PhQuestion } from '@phosphor-icons/vue';

defineProps({
  title: String,
});

const showingSidebar = ref(false);

const navigation = [
  { name: 'Dashboard', href: '/dashboard', icon: PhHouse },
  { name: 'Agenda', href: '/agenda', icon: PhCalendar },
  { name: 'Lançamentos', href: '/lancamentos', icon: PhClipboardText },
  { name: 'Clientes', href: '/clientes', icon: PhUsers },
  { name: 'Profissionais', href: '/profissionais', icon: PhUserCircle },
  { name: 'Serviços', href: '/servicos', icon: PhScissors },
  { name: 'Caixa', href: '/caixa', icon: PhWallet },
  { name: 'Relatórios', href: '/relatorios', icon: PhChartLine },
  { name: 'Suporte', href: '/suporte', icon: PhQuestion },
];

const logout = () => {
  router.post(route('logout'));
};
</script>

<template>
  <div class="min-h-screen bg-surface dark:bg-zinc-950">
    <Head :title="title" />

    <!-- Mobile sidebar backdrop -->
    <div
      v-if="showingSidebar"
      class="fixed inset-0 z-40 bg-black/50 lg:hidden"
      @click="showingSidebar = false"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed top-0 left-0 z-50 h-screen w-64 bg-white dark:bg-zinc-900 border-r border-border/50 transform transition-transform duration-300 lg:translate-x-0',
        showingSidebar ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <!-- Logo -->
      <div class="h-16 flex items-center justify-center border-b border-border/50">
        <Link href="/dashboard" class="flex items-center gap-2">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-500 to-fuchsia-600 flex items-center justify-center shadow-lg shadow-rose-500/20">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="1em" height="1em" fill="currentColor" class="w-5 h-5 text-white">
              <g>
                <path d="M208,144a15.78,15.78,0,0,1-10.42,14.94L146,178l-19,51.62a15.92,15.92,0,0,1-29.88,0L78,178l-51.62-19a15.92,15.92,0,0,1,0-29.88L78,110l19-51.62a15.92,15.92,0,0,1,29.88,0L146,110l51.62,19A15.78,15.78,0,0,1,208,144ZM152,48h16V64a8,8,0,0,0,16,0V48h16a8,8,0,0,0,0-16H184V16a8,8,0,0,0-16,0V32H152a8,8,0,0,0,0,16Zm88,32h-8V72a8,8,0,0,0-16,0v8h-8a8,8,0,0,0,0,16h8v8a8,8,0,0,0,16,0V96h8a8,8,0,0,0,0-16Z"></path>
              </g>
            </svg>
          </div>
          <span class="text-xl font-bold tracking-tight text-text dark:text-white">Nezor</span>
        </Link>
        <button
          @click="showingSidebar = false"
          class="lg:hidden absolute right-4 p-2 text-muted hover:text-text dark:hover:text-white"
        >
          <PhX class="w-5 h-5" />
        </button>
      </div>

      <!-- Navigation -->
      <nav class="p-4 space-y-1">
        <Link
          v-for="item in navigation"
          :key="item.name"
          :href="item.href"
          class="flex items-center gap-3 px-4 py-3 rounded-xl text-muted hover:text-primary hover:bg-primary/5 transition-all group"
          active-class="bg-primary/10 text-primary"
        >
          <component :is="item.icon" class="w-5 h-5 group-hover:scale-110 transition-transform" />
          <span class="font-medium">{{ item.name }}</span>
        </Link>
      </nav>

      <!-- Bottom section -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-border/50">
        <Link
          href="/settings"
          class="flex items-center gap-3 px-4 py-3 rounded-xl text-muted hover:text-primary hover:bg-primary/5 transition-all group"
        >
          <PhGear class="w-5 h-5" />
          <span class="font-medium">Configurações</span>
        </Link>
      </div>
    </aside>

    <!-- Main content -->
    <div class="lg:pl-64">
      <!-- Top bar -->
      <header class="h-16 bg-white dark:bg-zinc-900 border-b border-border/50 flex items-center justify-between px-4 sticky top-0 z-30">
        <div class="flex items-center gap-4">
          <button
            @click="showingSidebar = true"
            class="lg:hidden p-2 text-muted hover:text-text dark:hover:text-white"
          >
            <PhList class="w-6 h-6" />
          </button>
          <h1 class="text-lg font-semibold text-text dark:text-white">{{ title }}</h1>
        </div>

        <div class="flex items-center gap-3">
          <!-- Notifications -->
          <button class="p-2 text-muted hover:text-primary transition-colors relative">
            <PhBell class="w-5 h-5" />
            <span class="absolute top-1 right-1 w-2 h-2 bg-danger rounded-full"></span>
          </button>

          <!-- User menu -->
          <div class="flex items-center gap-3 pl-3 border-l border-border/50">
            <div class="text-right hidden sm:block">
              <p class="text-sm font-medium text-text dark:text-white">{{ $page.props.auth.user?.name }}</p>
              <p class="text-xs text-muted">Admin</p>
            </div>
            <button @click="logout" class="p-2 text-muted hover:text-danger transition-colors">
              <PhSignOut class="w-5 h-5" />
            </button>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>