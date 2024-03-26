<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted } from "vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { Modal } from "momentum-modal";
import Swal from "sweetalert2";

const props = defineProps({
    nilai: {
        type: Object,
    },
});

let datatable;

const formEdit = ref(false);

const form = useForm({
    persen_pemilik: props.nilai.persen_pemilik ?? 0,
    persen_karyawan: props.nilai.persen_karyawan ?? 0,
});

const submit = () => {
    form.post(route("nilai.store"), {
        onFinish: () => {
            // form.reset('password', 'password_confirmation');
        },
        onSuccess: () => {
            formEdit.value = false;
            Swal.fire({
                title: "Berhasil disimpan!",
                icon: "success",
                showCloseButton: true,
            });
        },
    });
};

const setupEventListeners = () => {
    $(document).on("click", ".edit-link", function (e) {
        e.preventDefault();
        const userId = $(this).data("user-id");
        router.get(
            route("categories.edit", { id: userId }),
            {},
            { preserveState: true }
        );
    });

    $(document).on("click", ".delete-link", function (e) {
        e.preventDefault();
        const userId = $(this).data("user-id");

        Swal.fire({
            title: "Yakin ingin menghapus?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            cancelButtonColor: "#d33",
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                axios
                    .delete(route("categories.destroy", { id: userId }))
                    .then((response) => {
                        Swal.fire("Berhasil dihapus!", "", "success");
                        datatable.ajax.reload(null, false);
                    })
                    .catch((error) => {
                        console.error("Failed to delete user:", error);
                    });
            }
        });
    });
};

const redrawDataTable = () => {
    if (datatable) {
        datatable.ajax.reload(null, false);
    }
};

const loadData = async () => {
    try {
        datatable = $("#datatables").DataTable({
            // responsive: true,
            lengthMenu: [
                [5, 10, 50, -1],
                [5, 10, 50, "All"],
            ],
            ajax: {
                url: route("categories.loadDatatables"),
            },
            columns: [
                { data: "name" },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-icon btn-default me-1" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item edit-link" href="#" data-user-id="${row.id}">Edit</a>
                                    <a class="dropdown-item delete-link" href="#" data-user-id="${row.id}">Delete</a>
                                </div>
                            </div>
                        `;
                    },
                },
            ],
        });

        setupEventListeners();
    } catch (error) {
        console.error("Gagal memuat data:", error);
    }
};

onMounted(() => {
    loadData();
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col align-self-center">
                            <h4 class="page-title pb-md-0">Nilai</h4>
                        </div>
                        <!--end col-->
                        <div class="col-auto align-self-center">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Nilai</li>
                            </ol>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
                <!--end page-title-box-->
            </div>
            <!--end col-->
        </div>

        <form @submit.prevent="submit" autocomplete="off" novalidate>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="">
                                <h3 class="card-title">Setup Nilai</h3>
                                <!-- <Link
                                :href="route('categories.create')"
                                :preserve-state="true"
                                class="btn btn-primary btn-icon-square-sm"
                                ><i class="fas fa-plus-circle"></i
                            ></Link> -->
                            </div>
                        </div>
                        <!--end card-header-->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Persentase Pemilik %</h6>
                                    <input
                                        id="date"
                                        class="form-control form-control-lg mb-3"
                                        :class="{
                                            'is-invalid':
                                                form.errors.persen_pemilik,
                                        }"
                                        type="text"
                                        v-model="form.persen_pemilik"
                                        :disabled="!formEdit"
                                    />
                                    <div class="invalid-feedback">
                                        {{ form.errors.persen_pemilik }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Persentase Karyawan %</h6>
                                    <input
                                        id="date"
                                        class="form-control form-control-lg mb-3"
                                        :class="{
                                            'is-invalid':
                                                form.errors.persen_karyawan,
                                        }"
                                        type="text"
                                        v-model="form.persen_karyawan"
                                        :disabled="!formEdit"
                                    />
                                    <div class="invalid-feedback">
                                        {{ form.errors.persen_karyawan }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button
                                    v-if="!formEdit"
                                    type="button"
                                    class="btn btn-secondary mt-2"
                                    style="width: 100px"
                                    @click="formEdit = true"
                                >
                                    Edit
                                </button>
                                <button
                                    v-if="formEdit"
                                    type="button"
                                    class="btn btn-secondary mt-2 me-2"
                                    style="width: 100px"
                                    @click="formEdit = false"
                                >
                                    Batal
                                </button>
                                <button
                                    v-if="formEdit"
                                    type="submit"
                                    class="btn btn-primary mt-2"
                                    style="width: 100px"
                                    :disabled="form.processing"
                                >
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </form>

        <Modal :redrawDataTable="redrawDataTable" />
    </AuthenticatedLayout>
</template>
