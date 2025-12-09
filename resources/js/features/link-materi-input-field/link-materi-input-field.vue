    <script setup lang="ts">
    import Button from '@/components/button.vue';
    import Input from '@/components/input.vue';
    import IcBaselineMinus from '@/icons/IcBaselineMinus.vue';
    import MaterialSymbolsAddCircleOutline from '@/icons/MaterialSymbolsAddCircleOutline.vue';
    import { motion } from 'motion-v';
    import { ref, watch } from 'vue';
    import { toast } from 'vue-sonner';
    const files = ref<{ links: string; name: string }[]>([
        {
            name: '',
            links: '',
        },
    ]);
    const total_ref = ref<number[]>([1]);
    let counter = 1;
    const emit = defineEmits(['update:modelValue']);
    const props = defineProps(['modelValue']);
    function addFile() {
        counter++;
        if (counter > 10) {
            toast.warning('Hanya bisa menyimpan 10 link materi');
        } else {
            total_ref.value.push(counter);
            files.value.push({
                name: '',
                links: '',
            }); // tambah 1 item kosong
        }
        emit('update:modelValue', files.value);
    }

    function removeFile(id: number, index: number) {
        const el = document.getElementById(`file-${id}`) as any;
        el.animate(
            [
                { opacity: 1, transform: 'translateY(0)' },
                { opacity: 0, transform: 'translateY(10px)' },
            ],
            { duration: 250, easing: 'ease' },
        ).onfinish = () => {
            total_ref.value.splice(index, 1);
            files.value.splice(index, 1);
            if (counter <= 1) {
                toast.warning('Minimal harus ada 1 file materi');
            } else {
                counter--;
            }
        };
        emit('update:modelValue', files.value);
    }
    watch(
        files,
        () => {
            emit('update:modelValue', files.value);
        },
        {
            deep: true,
        },
    );
    </script>
    <template>
        <div class="mt-0">
            <label class="mb-4 text-sm font-semibold text-neutral-600">Link Materi</label>

            <div class="flex flex-col py-1  overflow-y-auto pl-1 max-h-[200px] gap-1">
                <motion.div
                    :initial="{ opacity: 0, y: 10 }"
                    :animate="{ opacity: 1, y: 0 }"
                    :transition="{ duration: 0.25 }"
                    v-for="(id, index) in total_ref"
                    :key="id"
                    :id="`file-${id}`"
                    class="flex items-center gap-3"
                >
                <div class="flex items-center gap-1 max-w-full w-full">
                    <Input type="text" class="bg-white rounded-none text-xs  max-w-42 min-w-42 ring-1 ring-neutral-400" v-model="files[index].name" placeholder="Nama File Materi" />
                    <Input type="url" class="bg-white rounded-none text-xs  ring-1 ring-neutral-400" v-model="files[index].links" placeholder="Masukkan link materi" />
                
                </div>
                <div class="flex items-center gap-1">
                        <Button @click="addFile">
                            <MaterialSymbolsAddCircleOutline />
                        </Button>
                        <Button v-if="total_ref.length > 1" class="bg-red-500" @click="() => removeFile(id, index)">
                            <IcBaselineMinus />
                        </Button>
                    </div>
                </motion.div>
            </div>
        </div>
    </template>
