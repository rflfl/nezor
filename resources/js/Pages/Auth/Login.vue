<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PhEnvelope, PhLock, PhEye, PhEyeSlash } from '@phosphor-icons/vue';
import { ref } from 'vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const showPassword = ref(false);

const submit = () => {
  form.transform(data => ({
    ...data,
    remember: form.remember ? 'on' : '',
  })).post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <Head title="Entrar" />

  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo />
    </template>

    <div v-if="status" class="mb-4 p-3 rounded-lg bg-success/10 text-success text-sm font-medium">
      {{ status }}
    </div>

    <form @submit.prevent="submit" class="space-y-5">
      <div>
        <label class="block text-sm font-medium text-text dark:text-white mb-2">Email</label>
        <div class="relative">
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-muted">
            <PhEnvelope class="w-5 h-5" />
          </div>
          <input
            id="email"
            v-model="form.email"
            type="email"
            placeholder="seu@email.com"
            class="w-full pl-10 pr-4 py-3 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none"
            required
            autofocus
            autocomplete="username"
          />
        </div>
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div>
        <label class="block text-sm font-medium text-text dark:text-white mb-2">Senha</label>
        <div class="relative">
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-muted">
            <PhLock class="w-5 h-5" />
          </div>
          <input
            id="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="••••••••"
            class="w-full pl-10 pr-12 py-3 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none"
            required
            autocomplete="current-password"
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-primary transition-colors"
          >
            <PhEye v-if="!showPassword" class="w-5 h-5" />
            <PhEyeSlash v-else class="w-5 h-5" />
          </button>
        </div>
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer">
          <input
            type="checkbox"
            v-model="form.remember"
            class="w-4 h-4 rounded border-border text-primary focus:ring-primary/20"
          />
          <span class="text-sm text-muted">Lembrar-me</span>
        </label>
        <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-primary hover:text-primary/80 transition-colors">
          Esqueceu a senha?
        </Link>
      </div>

      <PrimaryButton
        type="submit"
        class="w-full justify-center"
        :class="{ 'opacity-50': form.processing }"
        :disabled="form.processing"
      >
        Entrar
      </PrimaryButton>

      <p class="text-center text-sm text-muted">
        Não tem conta?
        <Link :href="route('register')" class="text-primary font-medium hover:text-primary/80 transition-colors">
          Criar conta
        </Link>
      </p>
    </form>
  </AuthenticationCard>
</template>