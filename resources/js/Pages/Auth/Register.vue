<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PhUser, PhEnvelope, PhLock, PhEye, PhEyeSlash, PhBuildings, PhPhone, PhIdentificationCard } from '@phosphor-icons/vue';
import { ref } from 'vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useMask } from '@/composables/useMask';

const { maskPhone, maskCpfCnpj, unmask } = useMask();

const form = useForm({
  name: '',
  salon_name: '',
  phone: '',
  document: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <Head title="Criar Conta" />

  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo />
    </template>

    <form @submit.prevent="submit" class="space-y-5">
      <div>
        <label class="block text-sm font-medium text-text dark:text-white mb-2">Nome</label>
        <div class="relative">
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-muted">
            <PhUser class="w-5 h-5" />
          </div>
          <input
            id="name"
            v-model="form.name"
            type="text"
            placeholder="Seu nome completo"
            class="w-full pl-10 pr-4 py-3 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none"
            required
            autofocus
            autocomplete="name"
          />
        </div>
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

      <div>
        <label class="block text-sm font-medium text-text dark:text-white mb-2">Nome do Salão</label>
        <div class="relative">
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-muted">
            <PhBuildings class="w-5 h-5" />
          </div>
          <input
            id="salon_name"
            v-model="form.salon_name"
            type="text"
            placeholder="Nome do seu salão"
            class="w-full pl-10 pr-4 py-3 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none"
            required
          />
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-text dark:text-white mb-2">Telefone</label>
          <div class="relative">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-muted">
              <PhPhone class="w-5 h-5" />
            </div>
            <input
              id="phone"
              :value="form.phone"
              @input="e => form.phone = maskPhone(e.target.value)"
              type="tel"
              placeholder="(00) 00000-0000"
              maxlength="15"
              class="w-full pl-10 pr-4 py-3 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none"
              autocomplete="tel"
            />
          </div>
          <InputError class="mt-2" :message="form.errors.phone" />
        </div>

        <div>
          <label class="block text-sm font-medium text-text dark:text-white mb-2">CPF / CNPJ</label>
          <div class="relative">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-muted">
              <PhIdentificationCard class="w-5 h-5" />
            </div>
            <input
              id="document"
              :value="form.document"
              @input="e => form.document = maskCpfCnpj(e.target.value)"
              type="text"
              placeholder="000.000.000-00"
              maxlength="18"
              class="w-full pl-10 pr-4 py-3 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none"
            />
          </div>
          <InputError class="mt-2" :message="form.errors.document" />
        </div>
      </div>

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
            autocomplete="new-password"
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

      <div>
        <label class="block text-sm font-medium text-text dark:text-white mb-2">Confirmar Senha</label>
        <div class="relative">
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-muted">
            <PhLock class="w-5 h-5" />
          </div>
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            :type="showConfirmPassword ? 'text' : 'password'"
            placeholder="••••••••"
            class="w-full pl-10 pr-12 py-3 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none"
            required
            autocomplete="new-password"
          />
          <button
            type="button"
            @click="showConfirmPassword = !showConfirmPassword"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-primary transition-colors"
          >
            <PhEye v-if="!showConfirmPassword" class="w-5 h-5" />
            <PhEyeSlash v-else class="w-5 h-5" />
          </button>
        </div>
        <InputError class="mt-2" :message="form.errors.password_confirmation" />
      </div>

      <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="flex items-start gap-2">
        <input
          type="checkbox"
          v-model="form.terms"
          id="terms"
          class="mt-1 w-4 h-4 rounded border-border text-primary focus:ring-primary/20"
          required
        />
        <label for="terms" class="text-sm text-muted">
          Eu aceito os
          <a :href="route('terms.show')" target="_blank" class="text-primary hover:underline">Termos de Serviço</a>
          e a
          <a :href="route('policy.show')" target="_blank" class="text-primary hover:underline">Política de Privacidade</a>
        </label>
      </div>

      <PrimaryButton
        type="submit"
        class="w-full justify-center"
        :class="{ 'opacity-50': form.processing }"
        :disabled="form.processing"
      >
        Criar minha conta
      </PrimaryButton>

      <p class="text-center text-sm text-muted">
        Já tem conta?
        <Link :href="route('login')" class="text-primary font-medium hover:text-primary/80 transition-colors">
          Entrar
        </Link>
      </p>
    </form>
  </AuthenticationCard>
</template>
