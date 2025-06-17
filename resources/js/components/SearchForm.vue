<template>
     <form class="flex w-full max-w-md items-center gap-3 px-4">
        <Input v-model="searchQuery" placeholder="Search" class="flex-1" @keyup.enter="submitSearch" />
    </form>
</template>

<script setup lang="ts">
import Input from '@/components/ui/input/Input.vue';
import { ref } from 'vue';

const searchQuery = ref('');

const submitSearch = () => {
    console.log('Search:', searchQuery.value);
};
</script>
