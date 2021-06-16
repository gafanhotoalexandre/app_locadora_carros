<template>
    <!-- Início Card de Buscas -->
    <div class="card mb-3">
        <div class="card-header">Título - {{ titulo }}</div>

        <div class="card-body">
            <slot name="conteudo"></slot>
        </div>

        <div class="card-footer">
            <slot name="rodape"></slot>
        </div>
    </div>
    <!-- Fim Card de Buscas -->
</template>

<script>
    export default {
        props: ['titulo']
    }
</script>
