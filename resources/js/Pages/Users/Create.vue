<script setup>
import Swal from "sweetalert2";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useModal } from "momentum-modal";
import { Head, Link, useForm, router, usePage } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

const { show, close, redirect } = useModal();

const props = defineProps({
    roles: {
        type: Object,
    },
    redrawDataTable: {
        type: Function,
    },
});

const kelurahan = ref([]);
let modal;
let events;

const form = useForm({
    name: "",
    email: "",
    posisi: "",
    password: "",
    password_confirmation: "",
    role: "",
});

const submit = () => {
    form.post(route("users.store"), {
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
                        <h6>Role</h6>
                        <fieldset class="form-group">
                            <select
                                class="form-select"
                                id="role"
                                v-model="form.role"
                                :class="{ 'is-invalid': form.errors.role }"
                            >
                                <option value="">Pilih...</option>
                                <option
                                    v-for="role in roles"
                                    :value="role.id"
                                    :key="role.id"
                                >
                                    {{ role.display_name }}
                                </option>
                            </select>
                            <div class="invalid-feedback">
                                {{ form.errors.role }}
                            </div>
                        </fieldset>

                        <h6>Username</h6>
                        <div class="form-group">
                            <input
                                id="email"
                                type="text"
                                placeholder="Username"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.email }"
                                v-model="form.email"
                                autocomplete="off"
                            />
                            <div class="invalid-feedback">
                                {{ form.errors.email }}
                            </div>
                        </div>
                        <h6>Nama Lengkap</h6>
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
                        <h6>Posisi</h6>
                        <div class="form-group">
                            <input
                                id="posisi"
                                type="text"
                                placeholder="Posisi"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.posisi }"
                                v-model="form.posisi"
                                autocomplete="off"
                            />
                            <div class="invalid-feedback">
                                {{ form.errors.posisi }}
                            </div>
                        </div>

                        <h6>Password</h6>
                        <div class="form-group">
                            <input
                                id="password"
                                type="password"
                                placeholder="Password"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.password }"
                                v-model="form.password"
                                autocomplete="off"
                            />
                            <div class="invalid-feedback">
                                {{ form.errors.password }}
                            </div>
                        </div>
                        <h6>Konfirmasi Password</h6>
                        <div class="form-group">
                            <input
                                id="confirm-password"
                                type="password"
                                placeholder="Password"
                                class="form-control"
                                v-model="form.password_confirmation"
                                autocomplete="off"
                            />
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
