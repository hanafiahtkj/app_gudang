<script setup>
import Swal from "sweetalert2";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useModal } from "momentum-modal";
import { Head, Link, useForm, router, usePage } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

const { show, close, redirect } = useModal();

const props = defineProps({
    redrawDataTable: {
        type: Function,
    },
});

const kelurahan = ref([]);
let modal;
let events;

const form = useForm({
    name: "",
    address: "",
});

const submit = () => {
    form.post(route("warehouses.store"), {
        onFinish: () => {
            form.reset("password", "password_confirmation");
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
                        <h4 class="modal-title" id="myModalLabel33">Create</h4>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <h6>Nama</h6>
                        <div class="form-group">
                            <input
                                id="name"
                                type="text"
                                placeholder="Name"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.name }"
                                v-model="form.name"
                                autocomplete="off"
                            />
                            <div class="invalid-feedback">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <h6>Alamat</h6>
                        <div class="form-group">
                            <input
                                id="name"
                                type="text"
                                placeholder="Address"
                                class="form-control"
                                :class="{
                                    'is-invalid': form.errors.address,
                                }"
                                v-model="form.address"
                                autocomplete="off"
                            />
                            <div class="invalid-feedback">
                                {{ form.errors.address }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="background-color: #f6f8fb">
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
