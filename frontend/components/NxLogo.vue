<!-- Marca Nuxtavel: rounded-rect navy + malha hexagonal branca.
     variant="block"  → fundo sólido + malha branca (padrão)
     variant="mesh"   → só a malha, sem fundo (para usar sobre navy) -->
<script setup lang="ts">
const props = withDefaults(defineProps<{
  size?:    number
  variant?: 'block' | 'mesh'
  bg?:      string
  color?:   string
}>(), {
  size:    32,
  variant: 'block',
  bg:      '#084475',
})

const meshColor = computed(() =>
  props.variant === 'block' ? '#ffffff' : (props.color ?? 'currentColor')
)
const dotStroke = computed(() =>
  props.variant === 'block' ? props.bg : (props.color ?? 'currentColor')
)
</script>

<template>
  <svg :width="size" :height="size" viewBox="0 0 32 32" fill="none" aria-hidden="true">
    <!-- Fundo arredondado (só no variant block) -->
    <rect v-if="variant === 'block'" x="1" y="1" width="30" height="30" rx="6.5" :fill="bg" />

    <!-- Malha: anel hexagonal externo -->
    <line x1="16"    y1="6.5"   x2="24.23" y2="11.25" :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>
    <line x1="24.23" y1="11.25" x2="24.23" y2="20.75" :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>
    <line x1="24.23" y1="20.75" x2="16"    y2="25.5"  :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>
    <line x1="16"    y1="25.5"  x2="7.77"  y2="20.75" :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>
    <line x1="7.77"  y1="20.75" x2="7.77"  y2="11.25" :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>
    <line x1="7.77"  y1="11.25" x2="16"    y2="6.5"   :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>

    <!-- Raios: centro → cada vértice -->
    <line x1="16" y1="16" x2="16"    y2="6.5"   :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>
    <line x1="16" y1="16" x2="24.23" y2="11.25" :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>
    <line x1="16" y1="16" x2="24.23" y2="20.75" :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>
    <line x1="16" y1="16" x2="16"    y2="25.5"  :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>
    <line x1="16" y1="16" x2="7.77"  y2="20.75" :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>
    <line x1="16" y1="16" x2="7.77"  y2="11.25" :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>

    <!-- Triângulo alternado: pts[0]→pts[2]→pts[4]→pts[0] -->
    <line x1="16"    y1="6.5"   x2="24.23" y2="20.75" :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>
    <line x1="24.23" y1="20.75" x2="7.77"  y2="20.75" :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>
    <line x1="7.77"  y1="20.75" x2="16"    y2="6.5"   :stroke="meshColor" stroke-width="1.1" stroke-linecap="round"/>

    <!-- Nós nos 6 vértices -->
    <circle cx="16"    cy="6.5"   r="1.2" :fill="meshColor" :stroke="dotStroke" stroke-width="0.9"/>
    <circle cx="24.23" cy="11.25" r="1.2" :fill="meshColor" :stroke="dotStroke" stroke-width="0.9"/>
    <circle cx="24.23" cy="20.75" r="1.2" :fill="meshColor" :stroke="dotStroke" stroke-width="0.9"/>
    <circle cx="16"    cy="25.5"  r="1.2" :fill="meshColor" :stroke="dotStroke" stroke-width="0.9"/>
    <circle cx="7.77"  cy="20.75" r="1.2" :fill="meshColor" :stroke="dotStroke" stroke-width="0.9"/>
    <circle cx="7.77"  cy="11.25" r="1.2" :fill="meshColor" :stroke="dotStroke" stroke-width="0.9"/>
    <!-- Nó central -->
    <circle cx="16"    cy="16"    r="1.2" :fill="meshColor" :stroke="dotStroke" stroke-width="0.9"/>
  </svg>
</template>
