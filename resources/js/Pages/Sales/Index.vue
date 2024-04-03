<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Modal } from "momentum-modal";
import Swal from "sweetalert2";
import accounting from "accounting";

let datatable;

const setupEventListeners = () => {
    $(document).on("click", ".show-link", function (e) {
        e.preventDefault();
        const userId = $(this).data("user-id");
        router.get(
            route("sales.show", { id: userId }),
            {},
            { preserveState: true }
        );
    });

    $(document).on("click", ".edit-link", function (e) {
        e.preventDefault();
        const userId = $(this).data("user-id");
        router.get(
            route("sales.edit", { id: userId }),
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
                    .delete(route("sales.destroy", { id: userId }))
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
                url: route("sales.loadDatatables"),
            },
            columns: [
                { data: "sale_date" },
                { data: "warehouse.name" },
                { data: "total_products" },
                {
                    data: "total",
                    render: function (data, type, row) {
                        return formatCurrency(data);
                    },
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-icon btn-default me-1" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item show-link" href="#" data-user-id="${row.id}">Detail</a>
                                    <a class="dropdown-item delete-link" href="#" data-user-id="${row.id}">Hapus</a>
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
    <Head title="Pengeluaran" />

    <AuthenticatedLayout>
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col align-self-center">
                            <h4 class="page-title pb-md-0">Pengeluaran</h4>
                        </div>
                        <!--end col-->
                        <div class="col-auto align-self-center">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Pengeluaran
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
                            <Link
                                :href="route('sales.create')"
                                class="btn btn-primary btn-icon-square-sm"
                                ><i class="fas fa-plus-circle"></i
                            ></Link>
                        </div>
                    </div>
                    <!--end card-header-->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table
                                class="table table-striped"
                                id="datatables"
                                style="width: 100%"
                            >
                                <thead class="">
                                    <tr>
                                        <th style="max-width: 250px">
                                            Tanggal Pengeluaran
                                        </th>
                                        <th>Gudang</th>
                                        <th>Jumlah Produk</th>
                                        <th>Total Harga</th>
                                        <th>Aksi</th>
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
