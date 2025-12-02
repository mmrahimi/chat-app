<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps(['messages']);
const messages = ref([...props.messages]);

const form = useForm({
    body: '',
});

const send = () => {
    if (!form.body.trim()) {
        return;
    }

    form.post(route('chat.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('body');
        },
    });
};

onMounted(() => {
    window.Echo.channel('chat').listen('MessageSent', (e) => {
        messages.value.push(e.message);
    });
});

defineOptions({ layout: AppLayout });
</script>

<template>
    <div class="flex h-[calc(100vh-theme(spacing.16))] flex-col">
        <div class="flex-1 space-y-3 overflow-y-auto p-4">
            <div
                v-for="message in messages"
                :key="message.id"
                class="flex"
                :class="
                    message.user.id === $page.props.auth.user.id
                        ? 'justify-end'
                        : ''
                "
            >
                <div class="max-w-[70%] space-y-1">
                    <div
                        v-if="message.user.id !== $page.props.auth.user.id"
                        class="text-xs text-gray-500 dark:text-gray-400"
                    >
                        {{ message.user.name }}
                    </div>
                    <div
                        class="rounded-lg px-3 py-2 text-sm"
                        :class="
                            message.user.id === $page.props.auth.user.id
                                ? 'bg-blue-600 text-white'
                                : 'bg-gray-200 text-gray-900 dark:bg-gray-700 dark:text-gray-100'
                        "
                    >
                        {{ message.body }}
                    </div>
                </div>
            </div>
        </div>

        <form
            @submit.prevent="send"
            class="flex gap-2 border-t p-3 dark:border-gray-700"
        >
            <input
                v-model="form.body"
                placeholder="Type a message..."
                class="flex-1 rounded-lg border bg-transparent px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-gray-700"
            />
            <button
                type="submit"
                :disabled="!form.body.trim()"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700 disabled:opacity-50"
            >
                Send
            </button>
        </form>
    </div>
</template>

<style scoped></style>
