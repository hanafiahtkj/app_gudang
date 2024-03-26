<script setup>
import Swal from "sweetalert2";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useModal } from "momentum-modal";
import { Head, Link, useForm, router, usePage } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

const { show, close, redirect } = useModal();

const props = defineProps({
    data: {
        type: Object,
    },
    redrawDataTable: {
        type: Function,
    },
});

let modal;
let events;

const form = useForm({
    name: "",
    unit: "",
    description: "",
    ...props.data,
});

const submit = () => {
    form.put(route("products.update", { id: props.data.id }), {
        onFinish: () => {
            // form.reset('password', 'password_confirmation');
        },
        onSuccess: () => {
            Swal.fire({
                title: "Berhasil disimpan!",
                icon: "success",
                showCloseButton: true,
            });
            events.addEventListener("hidden.bs.modal", (event) => {
                props.redrawDataTable();
            });
            modal.hide();
        },
    });
};

onMounted(() => {
    modal = new bootstrap.Modal(document.getElementById("inlineForm"));
    modal.show();

    events = document.getElementById("inlineForm");
    events.addEventListener("hidden.bs.modal", redirect);
});
</script>

<template>
    <form @submit.prevent="submit" autocomplete="off" novalidate>
        <div
            class="modal fade text-left"
            id="inlineForm"
            tabindex="-1"
            aria-labelledby="myModalLabel33"
            aria-hidden="true"
            style="display: none"
        >
            <div
                class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                role="document"
            >
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Edit</h4>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <h6>Nama Produk</h6>
                        <div class="form-group">
                            <input
                                id="name"
                                type="text"
                                placeholder=""
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.name }"
                                v-model="form.name"
                                autocomplete="off"
                            />
                            <div class="invalid-feedback">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <h6>Unit</h6>
                        <div class="form-group">
                            <input
                                id="name"
                                type="text"
                                placeholder=""
                                class="form-control"
                                :class="{
                                    'is-invalid': form.errors.unit,
                                }"
                                v-model="form.unit"
                                autocomplete="off"
                            />
                            <div class="invalid-feedback">
                                {{ form.errors.unit }}
                            </div>
                        </div>

                        <h6>Keterangan</h6>
                        <div class="form-group">
                            <textarea
                                id="name"
                                type="text"
                                placeholder=""
                                class="form-control"
                                :class="{
                                    'is-invalid': form.errors.description,
                                }"
                                v-model="form.description"
                                autocomplete="off"
                            ></textarea>
                            <div class="invalid-feedback">
                                {{ form.errors.description }}
                            </div>
                        </div>
                    </div>
                    <div
                        class="modal-footer mt-2"
                        style="background-color: #f6f8fb"
                    >
                        <button
                            type="button"
                            class="btn bg-white btn-outline-default border me-auto"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary ms-1"
                            :disabled="form.processing"
                        >
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Simpan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
