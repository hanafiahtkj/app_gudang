<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted, watch, computed, watchEffect } from "vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { Modal } from "momentum-modal";
import Swal from "sweetalert2";
import { format } from "date-fns";
import CurrencyInput from "@/Components/CurrencyInput.vue";
import accounting from "accounting";

const props = defineProps({
    warehouses: {
        type: Object,
    },
    products: {
        type: Object,
    },
});

const form = useForm({
    date: format(new Date(), "dd/MM/yyyy"),
    warehouse_id: "",
    total_amount: 0,
    purchase_details: [],
});

const submit = () => {
    form.post(route("purchases.store"), {
        onFinish: () => {
            // form.reset('password', 'password_confirmation');
        },
        onSuccess: () => {
            form.reset();
            Swal.fire({
                title: "Berhasil disimpan!",
                icon: "success",
                showCloseButton: true,
            });
        },
    });
};

const product_id = ref("");
let selectrWarehouse;
let selectrProduct;
let datatable;
let modal;

const addProduct = (id) => {
    const product = props.products.find((value) => value.id === id);

    // Periksa apakah produk sudah ada di dalam purchase_details
    const existingProductIndex = form.purchase_details.findIndex(
        (detail) => detail.product_id === id
    );
    if (existingProductIndex !== -1) {
        // Produk sudah ada, maka tidak perlu menambahkannya lagi
        console.log("Product already exists in purchase details.");
        return;
    }

    const tempPurchaseDetails = [...form.purchase_details];
    tempPurchaseDetails.push({
        id: "",
        product: product,
        product_id: product.id,
        quantity: 0,
        unitcost: 0,
        total: 0,
    });
    form.purchase_details = tempPurchaseDetails;
    product_id.value = "";
    // selectrProduct.clear();
};

const removeProduct = (index) => {
    const tempPurchaseDetails = [...form.purchase_details];
    tempPurchaseDetails.splice(index, 1);
    form.purchase_details = tempPurchaseDetails;
};

const totalAmount = computed(() => {
    let total = 0;
    form.purchase_details.forEach((detail) => {
        total += detail.total;
    });
    return total;
});

watchEffect(() => {
    form.total_amount = totalAmount.value;
});

watch(
    () => form.purchase_details,
    (newValue, oldValue) => {
        newValue.forEach((detail, index) => {
            const total = detail.quantity * detail.unitcost;
            form.purchase_details[index].total = total;
        });
    },
    { deep: true }
);

const formatCurrency = (value) => {
    // Memeriksa apakah value tidak null atau undefined
    if (value !== null && value !== undefined) {
        const decimalCount = (value.toString().split(".")[1] || "").length;
        return accounting.formatMoney(value, {
            symbol: "", // Tidak menampilkan simbol mata uang
            precision: decimalCount || 0, // Menampilkan 2 angka di belakang koma
            thousand: ",", // Menyusun ribuan dengan titik
            decimal: ".", // Menyusun desimal dengan koma
        });
    } else {
        // Mengembalikan string kosong jika value null atau undefined
        return "";
    }
};

onMounted(() => {
    // selectrWarehouse = new Selectr("#warehouse");

    // selectrProduct = new Selectr("#product");

    var elem = document.querySelector("#date");
    new Datepicker(elem, {
        format: "dd/mm/yyyy",
    });

    loadData();

    events = document.getElementById("modalProducts");
    events.addEventListener("show.bs.modal", redrawDataTable);

    modal = new bootstrap.Modal(events);
});

const redrawDataTable = () => {
    if (datatable) {
        datatable.ajax.reload(null, false);
    }
};

const loadData = async () => {
    try {
        datatable = $("#datatables").DataTable({
            // responsive: true,
            select: true,
            lengthMenu: [
                [5, 10, 50, -1],
                [5, 10, 50, "All"],
            ],
            ajax: {
                url: route("products.loadDatatables"),
                data: function (d) {
                    // d.warehouse_id = form.warehouse_id;
                    d.product_ids = form.purchase_details.map(
                        (detail) => detail.product_id
                    );
                },
            },
            columns: [
                { data: "name" },
                { data: "unit" },
                { data: "stock" },
                // {
                //     data: "unitcost",
                //     render: function (data, type, row) {
                //         return formatCurrency(data);
                //     },
                // },
            ],
        });

        datatable.on("select", function (e, dt, type, indexes) {
            let rowData = datatable.rows(indexes).data();
            addProduct(rowData[0].id);
            modal.hide();
        });

        // setupEventListeners();
    } catch (error) {
        console.error("Gagal memuat data:", error);
    }
};
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

        <form @submit.prevent="submit" autocomplete="off" novalidate>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-3">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">Pemasukan Baru</h5>
                                <Link
                                    :href="route('purchases.index')"
                                    class="btn-close"
                                ></Link>
                            </div>
                        </div>
                        <!--end card-header-->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Tanggal Pemasukan</h6>
                                    <input
                                        id="date"
                                        class="form-control form-control-lg mb-3"
                                        :class="{
                                            'is-invalid': form.errors.date,
                                        }"
                                        type="text"
                                        v-model="form.date"
                                    />
                                    <div class="invalid-feedback">
                                        {{ form.errors.date }}
                                    </div>
                                </div>
                                <!-- end col -->
                                <div class="col-md-6">
                                    <h6>Gudang</h6>
                                    <div
                                        class="form-group"
                                        :class="{
                                            'group-invalid-feedback':
                                                form.errors.warehouse_id,
                                        }"
                                    >
                                        <select
                                            class="form-select form-select-lg"
                                            :class="{
                                                'is-invalid':
                                                    form.errors.warehouse_id,
                                            }"
                                            v-model="form.warehouse_id"
                                        >
                                            <option value="">
                                                Pilih gudang...
                                            </option>
                                            <option
                                                v-for="warehouse in warehouses"
                                                :value="warehouse.id"
                                                :key="warehouse.id"
                                            >
                                                {{ warehouse.name }}
                                            </option>
                                        </select>
                                        <p
                                            class="text-danger mt-0"
                                            v-if="form.errors.warehouse_id"
                                        >
                                            {{ form.errors.warehouse_id }}
                                        </p>
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->

                            <!-- <div class="row">
                                <div class="col-md-12">
                                    <h6>Cari Produk</h6>
                                    <select
                                        id="product"
                                        v-model="product_id"
                                        @change="addProduct(product_id)"
                                    >
                                        <option
                                            v-for="product in products"
                                            :value="product.id"
                                            :key="product.id"
                                        >
                                            {{ product.name }}
                                        </option>
                                    </select>
                                </div>
                            </div> -->
                            <!-- end row -->

                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="text-end">
                                        <button
                                            type="button"
                                            class="btn btn-de-dashed-primary btn-lg mb-3"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalProducts"
                                        >
                                            <i class="fas fa-plus-circle"></i>
                                            Produk
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table
                                            class="table table-bordered mb-0"
                                        >
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Jumlah</th>
                                                    <th>Harga</th>
                                                    <th>Total</th>
                                                    <th class="text-center">
                                                        Aksi
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(
                                                        details, index
                                                    ) in form.purchase_details"
                                                    :key="index"
                                                >
                                                    <td>
                                                        <input
                                                            class="form-control"
                                                            type="text"
                                                            value=""
                                                            disabled
                                                            v-model="
                                                                form
                                                                    .purchase_details[
                                                                    index
                                                                ].product.name
                                                            "
                                                        />
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="input-group"
                                                        >
                                                            <CurrencyInput
                                                                class="form-control"
                                                                :class="{
                                                                    'is-invalid':
                                                                        form
                                                                            .errors[
                                                                            'purchase_details.' +
                                                                                index +
                                                                                '.quantity'
                                                                        ],
                                                                }"
                                                                type="text"
                                                                v-model="
                                                                    form
                                                                        .purchase_details[
                                                                        index
                                                                    ].quantity
                                                                "
                                                            />
                                                            <span
                                                                class="input-group-text"
                                                                id="basic-addon2"
                                                                >{{
                                                                    form
                                                                        .purchase_details[
                                                                        index
                                                                    ].product
                                                                        .unit
                                                                }}</span
                                                            >
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <CurrencyInput
                                                            class="form-control"
                                                            :class="{
                                                                'is-invalid':
                                                                    form.errors[
                                                                        'purchase_details.' +
                                                                            index +
                                                                            '.unitcost'
                                                                    ],
                                                            }"
                                                            type="text"
                                                            v-model="
                                                                form
                                                                    .purchase_details[
                                                                    index
                                                                ].unitcost
                                                            "
                                                        />
                                                    </td>
                                                    <td>
                                                        <CurrencyInput
                                                            class="form-control"
                                                            :class="{
                                                                'is-invalid':
                                                                    form.errors[
                                                                        'purchase_details.' +
                                                                            index +
                                                                            '.total'
                                                                    ],
                                                            }"
                                                            type="text"
                                                            disabled
                                                            v-model="
                                                                form
                                                                    .purchase_details[
                                                                    index
                                                                ].total
                                                            "
                                                        />
                                                    </td>
                                                    <td class="text-center">
                                                        <a
                                                            href="#"
                                                            @click="
                                                                removeProduct(
                                                                    index
                                                                )
                                                            "
                                                            ><i
                                                                class="las la-trash-alt text-secondary font-16"
                                                            ></i
                                                        ></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th
                                                        colspan="3"
                                                        class="text-end"
                                                    >
                                                        Total Harga
                                                    </th>
                                                    <td>
                                                        <CurrencyInput
                                                            class="form-control"
                                                            :class="{
                                                                'is-invalid':
                                                                    form.errors
                                                                        .total_amount,
                                                            }"
                                                            type="text"
                                                            v-model="
                                                                form.total_amount
                                                            "
                                                            disabled
                                                        />
                                                    </td>

                                                    <td
                                                        class="text-center"
                                                    ></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!--end /table-->
                                    </div>
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary btn-lg w-100 h-45 mt-4"
                                :disabled="form.processing"
                            >
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </form>

        <!-- <Modal :redrawDataTable="redrawDataTable" /> -->

        <div
            class="modal fade"
            id="modalProducts"
            tabindex="-1"
            role="dialog"
            aria-labelledby="modalProductsLabel"
            aria-hidden="true"
        >
            <div
                class="modal-dialog modal-lg modal-dialog-centered"
                role="document"
            >
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title m-0" id="modalProductsLabel">
                            Produk
                        </h6>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <!--end modal-header-->
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table
                                class="table"
                                id="datatables"
                                style="width: 100%"
                            >
                                <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Unit</th>
                                        <th>Stock</th>
                                        <!-- <th>Harga</th> -->
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <!--end modal-body-->
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-de-secondary btn-sm"
                            data-bs-dismiss="modal"
                        >
                            Close
                        </button>
                    </div>
                    <!--end modal-footer-->
                </div>
                <!--end modal-content-->
            </div>
            <!--end modal-dialog-->
        </div>
        <!--end modal-->
    </AuthenticatedLayout>
</template>
