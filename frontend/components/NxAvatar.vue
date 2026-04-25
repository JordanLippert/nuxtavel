<script setup lang="ts">
const props = defineProps<{
  name: string
  avatarUrl?: string | null
  size?: 'sm' | 'md' | 'lg'
}>()

const initials = computed(() =>
  props.name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((p) => p[0])
    .join('')
    .toUpperCase()
)

const sizeClass = computed(() =>
  props.size === 'lg' ? 'nx-avatar nx-avatar-lg' : 'nx-avatar'
)
</script>

<template>
  <div :class="sizeClass">
    <img
      v-if="avatarUrl"
      :src="avatarUrl"
      :alt="name"
      style="width:100%;height:100%;border-radius:50%;object-fit:cover;"
    />
    <span v-else>{{ initials }}</span>
  </div>
</template>
