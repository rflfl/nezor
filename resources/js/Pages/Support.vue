<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import { PhQuestion, PhList, PhArrowUp } from '@phosphor-icons/vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps<{
  content: string;
}>();

const headings = ref<{ id: string; text: string; level: number }[]>([]);
const activeHeading = ref('');

function slugify(text: string): string {
  return text
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-');
}

onMounted(() => {
  const container = document.getElementById('support-content');
  if (!container) return;

  const h2s = container.querySelectorAll('h2');

  headings.value = Array.from(h2s).map((h) => {
    const text = h.textContent?.trim() || '';
    const id = slugify(text);
    h.id = id;
    return { id, text, level: 2 };
  });

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          activeHeading.value = entry.target.id;
        }
      });
    },
    { rootMargin: '-80px 0px -70% 0px' }
  );

  h2s.forEach((h) => observer.observe(h));
});

const scrollTo = (id: string) => {
  const el = document.getElementById(id);
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
};

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>

<template>
  <AppLayout title="Suporte">
    <Head title="Suporte" />

    <div class="max-w-7xl mx-auto">
      <div class="lg:grid lg:grid-cols-4 lg:gap-8">
        <!-- Sidebar Navigation -->
        <aside class="hidden lg:block lg:col-span-1">
          <nav class="sticky top-24 space-y-1">
            <div class="mb-4">
              <h3 class="text-sm font-semibold text-muted uppercase tracking-wider">
                Conteúdo
              </h3>
            </div>
            <button
              v-for="heading in headings"
              :key="heading.id"
              @click="scrollTo(heading.id)"
              :class="[
                'w-full text-left px-3 py-2 rounded-lg text-sm transition-all',
                activeHeading === heading.id
                  ? 'bg-primary/10 text-primary font-medium'
                  : 'text-muted hover:text-text hover:bg-surface',
              ]"
            >
              {{ heading.text }}
            </button>
          </nav>
        </aside>

        <!-- Mobile Navigation -->
        <div class="lg:hidden mb-6">
          <details class="group bg-white dark:bg-zinc-900 rounded-xl border border-border/50">
            <summary class="flex items-center gap-3 px-4 py-3 cursor-pointer list-none">
              <PhList class="w-5 h-5 text-muted" />
              <span class="font-medium text-text dark:text-white">Sumário</span>
            </summary>
            <div class="px-4 pb-3 space-y-1">
              <button
                v-for="heading in headings"
                :key="heading.id"
                @click="scrollTo(heading.id)"
                class="block w-full text-left px-3 py-2 rounded-lg text-sm text-muted hover:text-text hover:bg-surface transition-all"
              >
                {{ heading.text }}
              </button>
            </div>
          </details>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-border/50 shadow-sm">
            <div class="p-6 lg:p-10">
              <!-- Header -->
              <div class="flex items-center gap-3 mb-8 pb-6 border-b border-border/50">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                  <PhQuestion class="w-5 h-5 text-primary" />
                </div>
                <div>
                  <h1 class="text-2xl font-bold text-text dark:text-white">Central de Suporte</h1>
                  <p class="text-sm text-muted">Documentação completa do Nezor</p>
                </div>
              </div>

              <!-- Content -->
              <div
                id="support-content"
                class="prose dark:prose-invert max-w-none
                  prose-headings:text-text dark:prose-headings:text-white
                  prose-h2:text-xl prose-h2:font-semibold prose-h2:mt-8 prose-h2:mb-4
                  prose-p:text-muted-foreground
                  prose-a:text-primary prose-a:no-underline hover:prose-a:underline
                  prose-strong:text-text dark:prose-strong:text-white
                  prose-blockquote:border-l-primary prose-blockquote:bg-primary/5 prose-blockquote:py-2 prose-blockquote:px-4 prose-blockquote:rounded-r-lg
                  prose-table:text-sm prose-th:text-left prose-th:font-semibold prose-th:text-text dark:prose-th:text-white prose-td:text-muted-foreground
                  prose-li:text-muted-foreground
                  prose-hr:border-border/50
                  prose-code:text-primary prose-code:bg-primary/10 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:text-sm
                  prose-pre:bg-zinc-950 prose-pre:text-zinc-100"
                v-html="content"
              />

              <!-- Back to top -->
              <div class="mt-12 pt-6 border-t border-border/50 flex justify-center">
                <button
                  @click="scrollToTop"
                  class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-muted hover:text-primary hover:bg-primary/5 transition-all"
                >
                  <PhArrowUp class="w-4 h-4" />
                  Voltar ao topo
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
