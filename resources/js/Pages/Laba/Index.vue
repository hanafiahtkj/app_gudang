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
const bulantahun = ref(format(new Date(), "MM/yyyy"));

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
            route("laba.edit", { id: userId }),
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
                    .delete(route("laba.destroy", { id: userId }))
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
                data: function (d) {
                    d.bulantahun = bulantahun.value;
                },
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
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <div class="button-items">
                                <button type="button" class="btn btn-secondary edit-link" data-user-id="${row.id}">Edit</button>
                                <button type="button" class="btn btn-danger delete-link" data-user-id="${row.id}">Hapus</button>
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

    // var elem = document.querySelector("#date");
    // new Datepicker(elem, {
    //     format: "dd/mm/yyyy",
    // });

    // elem.addEventListener("changeDate", (event) => {
    //     form.tanggal = event.target.value;
    //     loadProsesData();
    // });

    var elem2 = document.querySelector("#inputBulanTahun");
    new Datepicker(elem2, {
        format: "mm/yyyy",
        pickLevel: 1,
    });

    elem2.addEventListener("changeDate", (event) => {
        bulantahun.value = event.target.value;
        redrawDataTable();
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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="text-end">
                            <!-- <h5 class="card-title">Periode</h5> -->
                            <Link
                                :href="route('laba.create')"
                                class="btn btn-primary"
                                ><i class="fas fa-plus-circle"></i> Tambah</Link
                            >
                        </div>
                    </div>
                    <!--end card-header-->
                    <div class="card-body">
                        <h6>Filter Bulan & Tahun</h6>
                        <div class="form-group mb-3">
                            <input
                                type="text"
                                placeholder=""
                                class="form-control"
                                v-model="bulantahun"
                                autocomplete="off"
                                id="inputBulanTahun"
                            />
                        </div>
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
                                        <th style="min-width: 180px">Aksi</th>
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
