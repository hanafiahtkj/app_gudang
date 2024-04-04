<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted, watch } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Modal } from "momentum-modal";
import Swal from "sweetalert2";
import accounting from "accounting";
import { format } from "date-fns";

let datatable;
const bulantahun = ref(format(new Date(), "MM/yyyy"));

const setupEventListeners = () => {
    $(document).on("click", ".show-link", function (e) {
        e.preventDefault();
        const userId = $(this).data("user-id");
        router.get(
            route("purchases.show", { id: userId }),
            {},
            { preserveState: true }
        );
    });

    $(document).on("click", ".edit-link", function (e) {
        e.preventDefault();
        const userId = $(this).data("user-id");
        router.get(
            route("purchases.edit", { id: userId }),
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
                    .delete(route("purchases.destroy", { id: userId }))
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
                url: route("purchases.loadDatatables"),
                data: function (d) {
                    d.bulantahun = bulantahun.value;
                },
            },
            columns: [
                { data: "date" },
                { data: "warehouse.name" },
                {
                    data: "total_amount",
                    render: function (data, type, row) {
                        return formatCurrency(data);
                    },
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <div class="button-items">
                                <button type="button" class="btn btn-dark show-link" data-user-id="${row.id}">Detail</button>
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
});
</script>

<template>
    <Head title="Pemasukan" />

    <AuthenticatedLayout>
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col align-self-center">
                            <h4 class="page-title pb-md-0">Pemasukan</h4>
                        </div>
                        <!--end col-->
                        <div class="col-auto align-self-center">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Pemasukan
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
                                :href="route('purchases.create')"
                                class="btn btn-primary btn-icon-square-sm"
                                ><i class="fas fa-plus-circle"></i
                            ></Link>
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
                                            Tanggal Pemasukan
                                        </th>
                                        <th>Gudang</th>
                                        <th>Total Harga</th>
                                        <th style="min-width: 250px">Aksi</th>
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
