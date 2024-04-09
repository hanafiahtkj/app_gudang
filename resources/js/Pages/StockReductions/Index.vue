<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Modal } from "momentum-modal";
import Swal from "sweetalert2";
import accounting from "accounting";
import { format } from "date-fns";

let datatable;
const bulantahun = ref(format(new Date(), "MM/yyyy"));
const today = ref(format(new Date(), "dd/MM/yyyy"));

const setupEventListeners = () => {
    $(document).on("click", ".show-link", function (e) {
        e.preventDefault();
        const userId = $(this).data("user-id");
        router.get(
            route("stock-reduction.show", { id: userId }),
            {},
            { preserveState: true }
        );
    });

    $(document).on("click", ".edit-link", function (e) {
        e.preventDefault();
        const userId = $(this).data("user-id");
        router.get(
            route("stock-reduction.edit", { id: userId }),
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
                    .delete(route("stock-reduction.destroy", { id: userId }))
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
                url: route("stock-reduction.loadDatatables"),
                data: function (d) {
                    d.bulantahun = bulantahun.value;
                },
            },
            columns: [
                { data: "stock_reduction_date" },
                { data: "warehouse.name" },
                { data: "total_products" },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <div class="button-items">
                                <button type="button" class="btn btn-dark show-link" data-user-id="${row.id}">Detail</button>
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

onMounted(() => {
    loadData();

    var elem = document.querySelector("#inputBulanTahun");
    new Datepicker(elem, {
        format: "mm/yyyy",
        pickLevel: 1,
    });

    elem.addEventListener("changeDate", (event) => {
        bulantahun.value = event.target.value;
        redrawDataTable();
    });

    var elem2 = document.querySelector("#inputToday");
    new Datepicker(elem2, {
        format: "dd/mm/yyyy",
    });
});
</script>

<template>
    <Head title="Penyusutan" />

    <AuthenticatedLayout>
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col align-self-center">
                            <h4 class="page-title pb-md-0">Penyusutan</h4>
                        </div>
                        <!--end col-->
                        <div class="col-auto align-self-center">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Penyusutan
                                </li>
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
                            <button
                                type="button"
                                class="btn btn-success me-2"
                                data-bs-toggle="modal"
                                data-bs-target="#laporanModal"
                            >
                                <i class="fas fa-download"></i> PDF
                            </button>
                            <Link
                                :href="route('stock-reduction.create')"
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
                                class="table table-striped"
                                id="datatables"
                                style="width: 100%"
                            >
                                <thead class="">
                                    <tr>
                                        <th style="max-width: 250px">
                                            Tanggal Penyusutan
                                        </th>
                                        <th>Gudang</th>
                                        <th>Jumlah Produk</th>
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

        <!-- Modal -->
        <form
            method="GET"
            :action="route('stock-reduction.laporanPdf')"
            target="_blank"
        >
            <div
                class="modal fade"
                id="laporanModal"
                tabindex="-1"
                aria-labelledby="laporanModalLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="laporanModalLabel">
                                Laporan
                            </h1>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <h6>Tanggal</h6>
                            <div class="form-group mb-3">
                                <input
                                    type="text"
                                    placeholder=""
                                    class="form-control"
                                    autocomplete="off"
                                    id="inputToday"
                                    v-model="today"
                                    name="today"
                                />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Unduh PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <Modal :redrawDataTable="redrawDataTable" />
    </AuthenticatedLayout>
</template>
