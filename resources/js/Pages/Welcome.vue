<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import { PhArrowRight, PhCalendarCheck, PhUsers, PhChartLineUp, PhCoins, PhScissors, PhSparkle, PhCheckCircle, PhStar, PhQuotes, PhCaretDown } from '@phosphor-icons/vue';
import { Link } from '@inertiajs/vue3';

const isScrolled = ref(false);

onMounted(() => {
  window.addEventListener('scroll', () => {
    isScrolled.value = window.scrollY > 20;
  });
});

// FAQ
const activeFaq = ref<number | null>(null);
function toggleFaq(index: number) {
  activeFaq.value = activeFaq.value === index ? null : index;
}

const faqItems = [
  {
    question: 'O Nezor é gratuito?',
    answer: 'Sim! Você pode usar o Nezor gratuitamente para testar todas as funcionalidades. Para salões com volume maior de atendimentos, oferecemos planos acessíveis com recursos avançados.',
  },
  {
    question: 'Preciso instalar algum programa?',
    answer: 'Não. O Nezor funciona 100% online pelo navegador. Basta acessar pelo computador, tablet ou celular. Seus dados ficam seguros na nuvem.',
  },
  {
    question: 'Como funciona o cálculo de comissões?',
    answer: 'Você cadastra cada serviço com o percentual da profissional. O Nezor calcula automaticamente o valor da comissão e o que fica para o salão em cada atendimento, sem planilhas.',
  },
  {
    question: 'Posso usar no celular?',
    answer: 'Sim. O Nezor é totalmente responsivo e funciona perfeitamente no celular, tablet ou computador. Ideal para consultar a agenda e lançar atendimentos de qualquer lugar.',
  },
  {
    question: 'Meus dados estão seguros?',
    answer: 'Com certeza. Utilizamos criptografia SSL, backup automático diário e seguimos as melhores práticas de segurança da indústria. Seus dados e os de suas clientes estão protegidos.',
  },
];

// Testimonials
const testimonials = [
  {
    name: 'Carla Mendes',
    role: 'Proprietária, Studio Carla',
    text: 'Depois que comecei a usar o Nezor, descobri que estava perdendo dinheiro com erros de comissão. Em um mês já vi diferença no lucro.',
  },
  {
    name: 'Fernanda Lopes',
    role: 'Gerente, Espaço Beleza',
    text: 'A agenda é super intuitiva. Minhas profissionais conseguem ver seus horários no celular e eu controlo tudo do balcão.',
  },
  {
    name: 'Juliana Costa',
    role: 'Proprietária, JC Hair',
    text: 'O fechamento de caixa que antes levava 30 minutos, agora faço em 5. O sistema calcula tudo sozinho. Indico para todas as amigas do ramo.',
  },
];

// Features
const features = [
  {
    icon: PhCalendarCheck,
    title: 'Agenda Inteligente',
    description: 'Organize atendimentos por dia, profissional e serviço. Visualização mensal, semanal e diária com um clique.',
    color: 'text-rose-400',
    bg: 'bg-rose-500/10',
  },
  {
    icon: PhUsers,
    title: 'Cadastro de Clientes',
    description: 'Mantenha o histórico completo de cada cliente, contato e preferências. Busca rápida por nome ou telefone.',
    color: 'text-fuchsia-400',
    bg: 'bg-fuchsia-500/10',
  },
  {
    icon: PhScissors,
    title: 'Catálogo de Serviços',
    description: 'Cadastre serviços com valores, duração e percentual de comissão. Controle o que cada profissional recebe.',
    color: 'text-amber-400',
    bg: 'bg-amber-500/10',
  },
  {
    icon: PhCoins,
    title: 'Caixa Diário',
    description: 'Abra e feche o caixa com controle de entradas, saídas e sangrias. Saiba exatamente o saldo do dia.',
    color: 'text-emerald-400',
    bg: 'bg-emerald-500/10',
  },
  {
    icon: PhChartLineUp,
    title: 'Relatórios Financeiros',
    description: 'Acompanhe faturamento, comissões e lucro mensal em tempo real. Tome decisões baseadas em dados.',
    color: 'text-sky-400',
    bg: 'bg-sky-500/10',
  },
  {
    icon: PhSparkle,
    title: 'Lançamento Rápido',
    description: 'Registre atendimentos em segundos. Encaixes, agendamentos e atendimentos sem marcação prévia.',
    color: 'text-violet-400',
    bg: 'bg-violet-500/10',
  },
];

// Steps
const steps = [
  {
    number: '01',
    title: 'Cadastre seu salão',
    description: 'Crie sua conta em menos de 2 minutos. Cadastre seus serviços, profissionais e configure as regras de comissão.',
  },
  {
    number: '02',
    title: 'Organize a agenda',
    description: 'Adicione clientes e comece a preencher a agenda com atendimentos. Tudo visível por dia e por profissional.',
  },
  {
    number: '03',
    title: 'Acompanhe o resultado',
    description: 'Veja em tempo real quanto entrou, quanto pagar de comissão e qual o lucro do seu salão. Sem planilhas.',
  },
];

// SEO + Schema
const jsonLd = computed(() => ({
  '@context': 'https://schema.org',
  '@type': 'SoftwareApplication',
  'name': 'Nezor',
  'applicationCategory': 'BusinessApplication',
  'description': 'Nezor é o sistema completo para gestão de salão de beleza. Controle agenda, clientes, caixa, comissões e lucro mensal em uma plataforma simples e moderna.',
  'operatingSystem': 'Web',
  'offers': {
    '@type': 'Offer',
    'price': '0',
    'priceCurrency': 'BRL',
  },
  'aggregateRating': {
    '@type': 'AggregateRating',
    'ratingValue': '4.9',
    'ratingCount': '128',
  },
}));
</script>

<template>
  <Head>
    <title>Nezor — Sistema para Salão de Beleza: Agenda, Caixa e Comissões</title>
    <meta name="description" content="Nezor é o sistema completo para gestão de salão de beleza. Controle agenda online, caixa diário, comissões automáticas, clientes e lucro mensal. Teste grátis." />
    <meta name="keywords" content="sistema para salão de beleza, gestão de salão, agenda de salão, controle de comissões, caixa de salão, software para cabeleireiro" />
    <meta property="og:title" content="Nezor — Sistema para Salão de Beleza" />
    <meta property="og:description" content="Agenda, caixa, comissões e clientes em um só lugar. Feito para salões que querem crescer." />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Nezor — Sistema para Salão de Beleza" />
    <meta name="twitter:description" content="Agenda, caixa, comissões e clientes em um só lugar." />
    <script type="application/ld+json">{{ JSON.stringify(jsonLd) }}</script>
  </Head>

  <div class="min-h-screen bg-slate-950 text-slate-100 overflow-x-hidden font-['Figtree']">
    <!-- Navbar -->
    <nav
      class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
      :class="isScrolled ? 'bg-slate-950/80 backdrop-blur-xl border-b border-white/5 py-3' : 'bg-transparent py-5'"
    >
      <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-500 to-fuchsia-600 flex items-center justify-center shadow-lg shadow-rose-500/20">
            <PhSparkle class="w-5 h-5 text-white" weight="fill" />
          </div>
          <span class="text-xl font-bold tracking-tight text-white">Nezor</span>
        </div>
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
          <a href="#recursos" class="hover:text-rose-400 transition-colors">Recursos</a>
          <a href="#como-funciona" class="hover:text-rose-400 transition-colors">Como Funciona</a>
          <a href="#depoimentos" class="hover:text-rose-400 transition-colors">Depoimentos</a>
          <a href="#faq" class="hover:text-rose-400 transition-colors">FAQ</a>
        </div>
        <div class="flex items-center gap-3">
          <Link
            href="/login"
            class="hidden sm:inline-flex text-sm font-medium text-slate-300 hover:text-white transition-colors"
          >
            Entrar
          </Link>
          <Link
            href="/register"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-full bg-gradient-to-r from-rose-500 to-fuchsia-600 hover:from-rose-400 hover:to-fuchsia-500 transition-all shadow-lg shadow-rose-500/25 hover:shadow-rose-500/40 hover:-translate-y-0.5"
          >
            Começar Grátis
            <PhArrowRight class="w-4 h-4" />
          </Link>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 md:pt-48 md:pb-32 overflow-hidden">
      <!-- Background blobs -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-[600px] h-[600px] rounded-full bg-rose-500/10 blur-[120px]" />
        <div class="absolute top-20 -left-20 w-[500px] h-[500px] rounded-full bg-fuchsia-600/10 blur-[100px]" />
        <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] rounded-full bg-amber-500/5 blur-[100px]" />
      </div>

      <div class="relative max-w-7xl mx-auto px-6">
        <div class="max-w-4xl mx-auto text-center">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm text-rose-300 mb-8 animate-fade-in">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
            </span>
            Novo — Teste grátis por 14 dias
          </div>

          <h1 class="font-['Playfair_Display'] text-5xl md:text-7xl lg:text-8xl font-bold leading-[1.1] tracking-tight mb-8">
            <span class="bg-gradient-to-r from-rose-300 via-fuchsia-300 to-amber-200 bg-clip-text text-transparent">
              Sua beleza,
            </span>
            <br />
            <span class="text-white">organizada.</span>
          </h1>

          <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
            O sistema mais elegante para salões de beleza. Agenda, caixa, comissões e clientes
            em uma plataforma que parece tão boa quanto o seu trabalho.
          </p>

          <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <Link
              href="/register"
              class="group inline-flex items-center gap-2 px-8 py-4 text-base font-semibold text-white rounded-full bg-gradient-to-r from-rose-500 to-fuchsia-600 hover:from-rose-400 hover:to-fuchsia-500 transition-all shadow-xl shadow-rose-500/25 hover:shadow-rose-500/50 hover:-translate-y-1"
            >
              Começar Grátis
              <PhArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
            </Link>
            <a
              href="#como-funciona"
              class="inline-flex items-center gap-2 px-8 py-4 text-base font-semibold text-slate-300 rounded-full border border-white/10 hover:border-white/20 hover:bg-white/5 transition-all"
            >
              Ver Como Funciona
            </a>
          </div>

          <div class="mt-16 flex items-center justify-center gap-8 text-slate-500 text-sm">
            <div class="flex items-center gap-2">
              <PhCheckCircle class="w-4 h-4 text-emerald-400" />
              <span>Sem cartão</span>
            </div>
            <div class="flex items-center gap-2">
              <PhCheckCircle class="w-4 h-4 text-emerald-400" />
              <span>Setup em 2 min</span>
            </div>
            <div class="flex items-center gap-2">
              <PhCheckCircle class="w-4 h-4 text-emerald-400" />
              <span>Cancele quando quiser</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Band -->
    <section class="relative border-y border-white/5 bg-white/[0.02]">
      <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
          <div>
            <div class="font-['Playfair_Display'] text-3xl md:text-4xl font-bold bg-gradient-to-r from-rose-400 to-fuchsia-400 bg-clip-text text-transparent">500+</div>
            <div class="text-sm text-slate-400 mt-1">Salões ativos</div>
          </div>
          <div>
            <div class="font-['Playfair_Display'] text-3xl md:text-4xl font-bold bg-gradient-to-r from-fuchsia-400 to-violet-400 bg-clip-text text-transparent">50k+</div>
            <div class="text-sm text-slate-400 mt-1">Atendimentos/mês</div>
          </div>
          <div>
            <div class="font-['Playfair_Display'] text-3xl md:text-4xl font-bold bg-gradient-to-r from-violet-400 to-sky-400 bg-clip-text text-transparent">4.9</div>
            <div class="text-sm text-slate-400 mt-1">Nota média</div>
          </div>
          <div>
            <div class="font-['Playfair_Display'] text-3xl md:text-4xl font-bold bg-gradient-to-r from-sky-400 to-emerald-400 bg-clip-text text-transparent">98%</div>
            <div class="text-sm text-slate-400 mt-1">Recomendam</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="recursos" class="py-24 md:py-32 relative">
      <div class="absolute top-1/2 left-0 w-[300px] h-[300px] rounded-full bg-fuchsia-600/5 blur-[120px] pointer-events-none" />
      
      <div class="max-w-7xl mx-auto px-6 relative">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <span class="text-rose-400 text-sm font-semibold tracking-wider uppercase">Recursos</span>
          <h2 class="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-white mt-3 mb-4">
            Tudo que seu salão precisa
          </h2>
          <p class="text-slate-400 text-lg">
            Ferramentas pensadas para o dia a dia de quem vive a beleza. Simples, rápido e bonito.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(feature, i) in features"
            :key="i"
            class="group relative p-8 rounded-3xl bg-slate-900/40 border border-white/5 hover:border-white/10 hover:bg-slate-900/60 transition-all duration-300 hover:-translate-y-1"
          >
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6" :class="feature.bg">
              <component :is="feature.icon" class="w-6 h-6" :class="feature.color" weight="duotone" />
            </div>
            <h3 class="text-xl font-semibold text-white mb-3">{{ feature.title }}</h3>
            <p class="text-slate-400 leading-relaxed">{{ feature.description }}</p>
            <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" />
          </div>
        </div>
      </div>
    </section>

    <!-- How it Works -->
    <section id="como-funciona" class="py-24 md:py-32 relative overflow-hidden">
      <div class="absolute top-0 right-0 w-[400px] h-[400px] rounded-full bg-rose-500/5 blur-[120px] pointer-events-none" />
      
      <div class="max-w-7xl mx-auto px-6 relative">
        <div class="text-center max-w-2xl mx-auto mb-20">
          <span class="text-fuchsia-400 text-sm font-semibold tracking-wider uppercase">Como Funciona</span>
          <h2 class="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-white mt-3 mb-4">
            Comece em 3 passos
          </h2>
          <p class="text-slate-400 text-lg">
            Deixe a planilha de lado. O Nezor foi feito por quem entende a rotina de um salão de beleza.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 relative">
          <!-- Connecting line for desktop -->
          <div class="hidden md:block absolute top-24 left-[16%] right-[16%] h-px bg-gradient-to-r from-rose-500/30 via-fuchsia-500/30 to-amber-500/30" />

          <div
            v-for="(step, i) in steps"
            :key="i"
            class="relative text-center"
          >
            <div class="relative inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-slate-900 border border-white/10 mb-6 z-10">
              <span class="font-['Playfair_Display'] text-2xl font-bold bg-gradient-to-br from-rose-400 to-fuchsia-500 bg-clip-text text-transparent">{{ step.number }}</span>
              <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-rose-500/10 to-fuchsia-500/10 opacity-0 hover:opacity-100 transition-opacity" />
            </div>
            <h3 class="text-xl font-semibold text-white mb-3">{{ step.title }}</h3>
            <p class="text-slate-400 leading-relaxed max-w-xs mx-auto">{{ step.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section id="depoimentos" class="py-24 md:py-32 relative border-y border-white/5 bg-white/[0.02]">
      <div class="absolute bottom-0 left-1/4 w-[300px] h-[300px] rounded-full bg-amber-500/5 blur-[120px] pointer-events-none" />
      
      <div class="max-w-7xl mx-auto px-6 relative">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <span class="text-amber-400 text-sm font-semibold tracking-wider uppercase">Depoimentos</span>
          <h2 class="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-white mt-3 mb-4">
            O que dizem nossas clientes
          </h2>
          <p class="text-slate-400 text-lg">
            Salões de todos os tamanhos estão economizando horas de trabalho manual e aumentando o lucro com o Nezor.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div
            v-for="(t, i) in testimonials"
            :key="i"
            class="relative p-8 rounded-3xl bg-slate-900/40 border border-white/5 hover:border-white/10 transition-all duration-300 hover:-translate-y-1"
          >
            <PhQuotes class="w-8 h-8 text-rose-500/30 mb-4" weight="fill" />
            <p class="text-slate-300 leading-relaxed mb-6 italic font-['Playfair_Display'] text-lg">
              "{{ t.text }}"
            </p>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-400 to-fuchsia-500 flex items-center justify-center text-white text-sm font-bold">
                {{ t.name.split(' ').map(n => n[0]).join('') }}
              </div>
              <div>
                <div class="text-white font-medium text-sm">{{ t.name }}</div>
                <div class="text-slate-500 text-xs">{{ t.role }}</div>
              </div>
            </div>
            <div class="flex gap-1 mt-4">
              <PhStar v-for="s in 5" :key="s" class="w-4 h-4 text-amber-400" weight="fill" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-24 md:py-32 relative">
      <div class="max-w-3xl mx-auto px-6">
        <div class="text-center mb-16">
          <span class="text-violet-400 text-sm font-semibold tracking-wider uppercase">Dúvidas</span>
          <h2 class="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-white mt-3 mb-4">
            Perguntas Frequentes
          </h2>
          <p class="text-slate-400 text-lg">
            Tudo que você precisa saber antes de começar.
          </p>
        </div>

        <div class="space-y-4">
          <div
            v-for="(item, i) in faqItems"
            :key="i"
            class="rounded-2xl border border-white/5 bg-slate-900/40 overflow-hidden transition-all duration-300"
            :class="activeFaq === i ? 'border-white/10 bg-slate-900/60' : 'hover:border-white/10'"
          >
            <button
              @click="toggleFaq(i)"
              class="w-full flex items-center justify-between p-6 text-left"
            >
              <span class="font-medium text-white pr-4">{{ item.question }}</span>
              <PhCaretDown
                class="w-5 h-5 text-slate-400 flex-shrink-0 transition-transform duration-300"
                :class="activeFaq === i ? 'rotate-180' : ''"
              />
            </button>
            <div
              class="overflow-hidden transition-all duration-300"
              :class="activeFaq === i ? 'max-h-96' : 'max-h-0'"
            >
              <p class="px-6 pb-6 text-slate-400 leading-relaxed">{{ item.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Final CTA -->
    <section class="py-24 md:py-32 relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-rose-500/10 via-fuchsia-600/10 to-amber-500/10 pointer-events-none" />
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-fuchsia-600/10 blur-[120px] pointer-events-none" />
      
      <div class="relative max-w-4xl mx-auto px-6 text-center">
        <h2 class="font-['Playfair_Display'] text-4xl md:text-6xl font-bold text-white mb-6">
          Pronta para transformar
          <span class="bg-gradient-to-r from-rose-300 via-fuchsia-300 to-amber-200 bg-clip-text text-transparent">
            seu salão?
          </span>
        </h2>
        <p class="text-lg md:text-xl text-slate-400 mb-10 max-w-2xl mx-auto">
          Junte-se a centenas de salões que já deixaram as planilhas para trás e escolheram crescer com inteligência.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
          <Link
            href="/register"
            class="group inline-flex items-center gap-2 px-10 py-4 text-lg font-semibold text-white rounded-full bg-gradient-to-r from-rose-500 to-fuchsia-600 hover:from-rose-400 hover:to-fuchsia-500 transition-all shadow-2xl shadow-rose-500/30 hover:shadow-rose-500/60 hover:-translate-y-1"
          >
            Criar Conta Grátis
            <PhArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
          </Link>
          <Link
            href="/login"
            class="inline-flex items-center gap-2 px-10 py-4 text-lg font-semibold text-slate-300 rounded-full border border-white/10 hover:border-white/20 hover:bg-white/5 transition-all"
          >
            Já tenho conta
          </Link>
        </div>
        <p class="text-slate-500 text-sm mt-6">Não precisa de cartão de crédito. Cancele quando quiser.</p>
      </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/5 py-12 bg-slate-950">
      <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-rose-500 to-fuchsia-600 flex items-center justify-center">
              <PhSparkle class="w-4 h-4 text-white" weight="fill" />
            </div>
            <span class="text-lg font-bold text-white">Nezor</span>
          </div>
          <div class="flex items-center gap-6 text-sm text-slate-500">
            <a href="#" class="hover:text-slate-300 transition-colors">Termos</a>
            <a href="#" class="hover:text-slate-300 transition-colors">Privacidade</a>
            <a href="#" class="hover:text-slate-300 transition-colors">Contato</a>
          </div>
          <p class="text-sm text-slate-600">&copy; {{ new Date().getFullYear() }} Nezor. Todos os direitos reservados.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.8s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
