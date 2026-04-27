<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { PhUser, PhLock, PhDeviceMobile, PhTrash, PhCheck, PhWarning, PhSignOut } from '@phosphor-icons/vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps<{
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      phone?: string;
    };
  };
}>();

const page = usePage();
const jetstreamFlash = computed(() => page.props.jetstream?.flash || {});
const showSuccess = ref(false);
const successMessage = ref('');

watch(() => jetstreamFlash.value, (flash) => {
  if (flash?.banner) {
    successMessage.value = flash.banner;
    showSuccess.value = true;
    setTimeout(() => showSuccess.value = false, 4000);
  }
}, { immediate: true, deep: true });

// Profile form
const profileForm = useForm({
  name: props.auth.user.name,
  email: props.auth.user.email,
  phone: props.auth.user.phone || '',
});

const updatingProfile = ref(false);

const updateProfile = () => {
  updatingProfile.value = true;
  profileForm.put(route('user-profile-information.update'), {
    preserveScroll: true,
    onSuccess: () => {
      updatingProfile.value = false;
    },
    onError: () => {
      updatingProfile.value = false;
    },
  });
};

// Password form
const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const updatingPassword = ref(false);

const updatePassword = () => {
  updatingPassword.value = true;
  passwordForm.put(route('user-password.update'), {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset();
      updatingPassword.value = false;
    },
    onError: () => {
      updatingPassword.value = false;
    },
  });
};

// Logout other sessions
const logoutOtherSessions = () => {
  router.post(route('other-browser-sessions.destroy'), {
    password: '',
  }, {
    preserveScroll: true,
  });
};

// Delete account
const showDeleteConfirm = ref(false);
const deleteForm = useForm({
  password: '',
});

const deleteAccount = () => {
  deleteForm.delete(route('current-user.destroy'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout title="Configurações">
    <Head title="Configurações" />

    <div class="max-w-3xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
          <PhUser class="w-5 h-5 text-primary" />
        </div>
        <div>
          <h1 class="text-2xl font-bold text-text dark:text-white">Configurações</h1>
          <p class="text-sm text-muted">Gerencie sua conta e preferências</p>
        </div>
      </div>

      <!-- Success Message -->
      <div
        v-if="showSuccess"
        class="bg-success/10 border border-success/30 rounded-xl p-4 flex items-center gap-3 animate-fade-in"
      >
        <PhCheck class="w-5 h-5 text-success flex-shrink-0" />
        <p class="text-sm text-success font-medium">{{ successMessage || 'Operação realizada com sucesso!' }}</p>
      </div>

      <!-- Profile Info -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-border/50 shadow-sm">
        <div class="p-6 border-b border-border/50">
          <h2 class="text-lg font-semibold text-text dark:text-white">Informações do Perfil</h2>
          <p class="text-sm text-muted">Atualize seus dados pessoais</p>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-text dark:text-white mb-1">Nome</label>
            <input
              v-model="profileForm.name"
              type="text"
              class="w-full px-4 py-2 rounded-lg border border-border/50 bg-surface dark:bg-zinc-800 text-text dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
            />
            <p v-if="profileForm.errors.name" class="mt-1 text-sm text-danger">{{ profileForm.errors.name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-text dark:text-white mb-1">E-mail</label>
            <input
              v-model="profileForm.email"
              type="email"
              class="w-full px-4 py-2 rounded-lg border border-border/50 bg-surface dark:bg-zinc-800 text-text dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
            />
            <p v-if="profileForm.errors.email" class="mt-1 text-sm text-danger">{{ profileForm.errors.email }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-text dark:text-white mb-1">Telefone</label>
            <input
              v-model="profileForm.phone"
              type="text"
              class="w-full px-4 py-2 rounded-lg border border-border/50 bg-surface dark:bg-zinc-800 text-text dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
            />
            <p v-if="profileForm.errors.phone" class="mt-1 text-sm text-danger">{{ profileForm.errors.phone }}</p>
          </div>
          <div class="flex justify-end pt-2">
            <button
              @click="updateProfile"
              :disabled="updatingProfile"
              class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50"
            >
              <PhCheck v-if="!updatingProfile" class="w-4 h-4" />
              <span v-if="updatingProfile">Salvando...</span>
              <span v-else>Salvar Perfil</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Change Password -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-border/50 shadow-sm">
        <div class="p-6 border-b border-border/50">
          <div class="flex items-center gap-2">
            <PhLock class="w-5 h-5 text-primary" />
            <h2 class="text-lg font-semibold text-text dark:text-white">Alterar Senha</h2>
          </div>
          <p class="text-sm text-muted">Mantenha sua conta segura com uma senha forte</p>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-text dark:text-white mb-1">Senha Atual</label>
            <input
              v-model="passwordForm.current_password"
              type="password"
              class="w-full px-4 py-2 rounded-lg border border-border/50 bg-surface dark:bg-zinc-800 text-text dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
            />
            <p v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-danger">{{ passwordForm.errors.current_password }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-text dark:text-white mb-1">Nova Senha</label>
            <input
              v-model="passwordForm.password"
              type="password"
              class="w-full px-4 py-2 rounded-lg border border-border/50 bg-surface dark:bg-zinc-800 text-text dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
            />
            <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-danger">{{ passwordForm.errors.password }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-text dark:text-white mb-1">Confirmar Nova Senha</label>
            <input
              v-model="passwordForm.password_confirmation"
              type="password"
              class="w-full px-4 py-2 rounded-lg border border-border/50 bg-surface dark:bg-zinc-800 text-text dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
            />
          </div>
          <div class="flex justify-end pt-2">
            <button
              @click="updatePassword"
              :disabled="updatingPassword"
              class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50"
            >
              <PhLock v-if="!updatingPassword" class="w-4 h-4" />
              <span v-if="updatingPassword">Atualizando...</span>
              <span v-else>Alterar Senha</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Browser Sessions -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-border/50 shadow-sm">
        <div class="p-6 border-b border-border/50">
          <div class="flex items-center gap-2">
            <PhDeviceMobile class="w-5 h-5 text-primary" />
            <h2 class="text-lg font-semibold text-text dark:text-white">Sessões do Navegador</h2>
          </div>
          <p class="text-sm text-muted">Encerre sessões ativas em outros dispositivos</p>
        </div>
        <div class="p-6">
          <button
            @click="logoutOtherSessions"
            class="inline-flex items-center gap-2 px-4 py-2 border border-border/50 rounded-lg text-muted hover:text-primary hover:bg-primary/5 transition-colors"
          >
            <PhSignOut class="w-4 h-4" />
            Encerrar Outras Sessões
          </button>
        </div>
      </div>

      <!-- Delete Account -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-danger/30 shadow-sm">
        <div class="p-6 border-b border-danger/20">
          <div class="flex items-center gap-2">
            <PhWarning class="w-5 h-5 text-danger" />
            <h2 class="text-lg font-semibold text-danger">Excluir Conta</h2>
          </div>
          <p class="text-sm text-muted">Esta ação é irreversível. Todos os dados serão perdidos.</p>
        </div>
        <div class="p-6">
          <button
            v-if="!showDeleteConfirm"
            @click="showDeleteConfirm = true"
            class="inline-flex items-center gap-2 px-4 py-2 bg-danger/10 text-danger rounded-lg hover:bg-danger hover:text-white transition-colors"
          >
            <PhTrash class="w-4 h-4" />
            Excluir Conta
          </button>

          <div v-else class="space-y-4">
            <p class="text-sm text-danger font-medium">Tem certeza? Digite sua senha para confirmar:</p>
            <input
              v-model="deleteForm.password"
              type="password"
              placeholder="Sua senha atual"
              class="w-full px-4 py-2 rounded-lg border border-danger/30 bg-surface dark:bg-zinc-800 text-text dark:text-white focus:ring-2 focus:ring-danger focus:border-transparent outline-none"
            />
            <p v-if="deleteForm.errors.password" class="text-sm text-danger">{{ deleteForm.errors.password }}</p>
            <div class="flex gap-3">
              <button
                @click="deleteAccount"
                class="inline-flex items-center gap-2 px-4 py-2 bg-danger text-white rounded-lg hover:bg-danger/90 transition-colors"
              >
                <PhTrash class="w-4 h-4" />
                Confirmar Exclusão
              </button>
              <button
                @click="showDeleteConfirm = false"
                class="px-4 py-2 border border-border/50 rounded-lg text-muted hover:text-text transition-colors"
              >
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
