<script setup lang="ts">
</script>

<template>
  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-surface dark:bg-zinc-950 px-4 py-12">
    <div class="mb-8 animate-scale-in">
      <slot name="logo" />
    </div>

    <div class="w-full sm:max-w-md bg-white dark:bg-zinc-900 shadow-2xl border border-border/50 rounded-2xl p-8 animate-slide-up">
      <slot />
    </div>
  </div>
</template>