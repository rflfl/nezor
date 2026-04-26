<script setup lang="ts">
import { cn } from '@/lib/utils';

interface Props {
  variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
  size?: 'default' | 'sm' | 'lg' | 'icon';
  as?: string;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
  size: 'default',
  as: 'button',
});

const variantClasses = {
  default: 'bg-primary text-white hover:bg-primary/90 shadow-sm',
  destructive: 'bg-danger text-white hover:bg-danger/90 shadow-sm',
  outline: 'border-2 border-primary bg-transparent hover:bg-primary/10 text-primary',
  secondary: 'bg-secondary text-text hover:bg-secondary/80',
  ghost: 'hover:bg-secondary/50',
  link: 'text-primary underline-offset-4 hover:underline',
};

const sizeClasses = {
  default: 'h-10 px-4 py-2',
  sm: 'h-9 rounded-md px-3',
  lg: 'h-11 rounded-md px-8',
  icon: 'h-10 w-10',
};
</script>

<template>
  <component
    :is="as"
    :class="cn(
      'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50',
      variantClasses[variant],
      sizeClasses[size],
      props.class
    )"
  >
    <slot />
  </component>
</template>