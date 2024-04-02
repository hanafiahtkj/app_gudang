<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted, watch } from "vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { Modal } from "momentum-modal";
import Swal from "sweetalert2";
import { format } from "date-fns";
import CurrencyInput from "@/Components/CurrencyInput.vue";
import accounting from "accounting";

const props = defineProps({
    nilai: {
        type: Object,
    },
});

let datatable;

const formEdit = ref(false);

const form = useForm({
    tanggal: format(new Date(), "dd/MM/yyyy"),
    penjualan: 0,
    pengeluaran: 0,
    laba: 0,
    persen_pemilik: props.nilai.persen_pemilik ?? 0,
    persen_karyawan: props.nilai.persen_karyawan ?? 0,
    laba_pemilik: 0,
    laba_karyawan: 0,
});

const submit = () => {
    form.post(route("laba.store"), {
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
            redrawDataTable();
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

const formatCurrency = (value) => {
    const decimalCount = (value.toString().split(".")[1] || "").length;
    return accounting.formatMoney(value, {
        symbol: "", // Tidak menampilkan simbol mata uang
        precision: decimalCount || 0, // Menampilkan 2 angka di belakang koma
        thousand: ",", // Menyusun ribuan dengan titik
        decimal: ".", // Menyusun desimal dengan koma
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
            responsive: true,
            dom:
                "<'row'<'col-3'l><'col-9'f>>" +
                "<'row dt-row'<'col-sm-12'tr>>" +
                "<'row'<'col-4'i><'col-8'p>>",
            language: {
                info: "Page _PAGE_ of _PAGES_",
                lengthMenu: "_MENU_ ",
                search: "",
                searchPlaceholder: "Search..",
            },
            lengthMenu: [
                [5, 10, 50, -1],
                [5, 10, 50, "All"],
            ],
            ajax: {
                url: route("laba.loadDatatables"),
            },
            columns: [
                { data: "tanggal" },
                {
                    data: "penjualan",
                    render: function (data, type, row) {
                        return formatCurrency(data);
                    },
                },
                {
                    data: "pengeluaran",
                    render: function (data, type, row) {
                        return formatCurrency(data);
                    },
                },
                {
                    data: "laba",
                    render: function (data, type, row) {
                        return formatCurrency(data);
                    },
                },
                {
                    data: "laba_pemilik",
                    render: function (data, type, row) {
                        return formatCurrency(data);
                    },
                },
                {
                    data: "laba_karyawan",
                    render: function (data, type, row) {
                        return formatCurrency(data);
                    },
                },
            ],
        });

        setupEventListeners();
    } catch (error) {
        console.error("Gagal memuat data:", error);
    }
};

const loadProsesData = async () => {
    let tanggal = form.tanggal;
    if (tanggal) {
        let url = route("laba.loadProsesData");
        url = url + `?tanggal=${tanggal}`;
        const response = await axios.get(url);
        if (response.status === 200) {
            const data = response.data.proses;
            console.log(data);
            if (data) {
                const { tanggal: tanggalData, ...restData } = data; // Membongkar tanggal dari data
                Object.assign(form, restData); // Mengganti nilai-nilai selain tanggal
            } else {
                form.reset();
                form.tanggal = tanggal;
            }
        }
    }
};

onMounted(() => {
    loadData();
    loadProsesData();

    var elem = document.querySelector("#date");
    new Datepicker(elem, {
        format: "dd/mm/yyyy",
    });

    elem.addEventListener("changeDate", (event) => {
        form.tanggal = event.target.value;
        loadProsesData();
    });
});

watch(
    () => form.penjualan,
    (newValue, oldValue) => {
        const total = newValue - form.pengeluaran;
        form.laba = total;
    }
);

watch(
    () => form.laba,
    (newValue, oldValue) => {
        form.laba_pemilik = (form.persen_pemilik / 100) * newValue;
        form.laba_karyawan = (form.persen_karyawan / 100) * newValue;
    }
);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col align-self-center">
                            <h4 class="page-title pb-md-0">Laba</h4>
                        </div>
                        <!--end col-->
                        <div class="col-auto align-self-center">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Laba</li>
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
                                <h3 class="card-title">Laba Harian</h3>
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
                                <div class="col-12">
                                    <h6>Tanggal</h6>
                                    <input
                                        id="date"
                                        class="form-control form-control-lg"
                                        :class="{
                                            'is-invalid': form.errors.tanggal,
                                        }"
                                        type="text"
                                        v-model="form.tanggal"
                                    />
                                    <div class="invalid-feedback">
                                        {{ form.errors.tanggal }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h6>Penjualan</h6>
                                    <CurrencyInput
                                        class="form-control form-control-lg"
                                        :class="{
                                            'is-invalid': form.errors.penjualan,
                                        }"
                                        type="text"
                                        v-model="form.penjualan"
                                        :disabled="!formEdit"
                                    />
                                    <div class="invalid-feedback">
                                        {{ form.errors.penjualan }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h6>Pengeluaran</h6>
                                    <CurrencyInput
                                        class="form-control form-control-lg"
                                        :class="{
                                            'is-invalid':
                                                form.errors.pengeluaran,
                                        }"
                                        type="text"
                                        v-model="form.pengeluaran"
                                        disabled
                                    />
                                    <div class="invalid-feedback">
                                        {{ form.errors.pengeluaran }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <h6>Laba</h6>
                                    <CurrencyInput
                                        class="form-control form-control-lg"
                                        :class="{
                                            'is-invalid': form.errors.laba,
                                        }"
                                        type="text"
                                        v-model="form.laba"
                                        :disabled="!formEdit"
                                    />
                                    <div class="invalid-feedback">
                                        {{ form.errors.laba }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-2">
                                            <h6>Persentase %</h6>
                                            <input
                                                class="form-control form-control-lg"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors
                                                            .persen_pemilik,
                                                }"
                                                type="text"
                                                v-model="form.persen_pemilik"
                                                disabled
                                            />
                                            <div class="invalid-feedback">
                                                {{ form.errors.persen_pemilik }}
                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <h6>Laba Pemilik</h6>
                                            <CurrencyInput
                                                class="form-control form-control-lg"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors
                                                            .laba_pemilik,
                                                }"
                                                type="text"
                                                v-model="form.laba_pemilik"
                                                :disabled="!formEdit"
                                            />
                                            <div class="invalid-feedback">
                                                {{ form.errors.laba_pemilik }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-2">
                                            <h6>Persentase %</h6>
                                            <input
                                                class="form-control form-control-lg"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors
                                                            .persen_karyawan,
                                                }"
                                                type="text"
                                                v-model="form.persen_karyawan"
                                                disabled
                                            />
                                            <div class="invalid-feedback">
                                                {{
                                                    form.errors.persen_karyawan
                                                }}
                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <h6>Laba Karyawan</h6>
                                            <CurrencyInput
                                                class="form-control form-control-lg"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors
                                                            .laba_karyawan,
                                                }"
                                                type="text"
                                                v-model="form.laba_karyawan"
                                                :disabled="!formEdit"
                                            />
                                            <div class="invalid-feedback">
                                                {{ form.errors.laba_karyawan }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end mt-3">
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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h3 class="card-title">Daftar Laba Harian</h3>
                        </div>
                    </div>
                    <!--end card-header-->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table
                                class="table"
                                id="datatables"
                                style="width: 100%"
                            >
                                <thead class="thead-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Penjualan</th>
                                        <th>Pengeluaran</th>
                                        <th>Laba</th>
                                        <th>Laba Pemilik</th>
                                        <th>Laba Karyawan</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->

        <Modal :redrawDataTable="redrawDataTable" />
    </AuthenticatedLayout>
</template>
