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
    sale_date: format(new Date(), "dd/MM/yyyy"),
    warehouse_id: "",
    total_products: 0,
    total: 0,
    sale_details: [],
});

const submit = () => {
    form.post(route("sales.store"), {
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

const addProduct = (product) => {
    const tempSaleDetails = [...form.sale_details];
    tempSaleDetails.push({
        id: "",
        product: product,
        product_id: product.id,
        quantity: 0,
        unitcost: product.unitcost,
        total: 0,
    });
    form.sale_details = tempSaleDetails;
    console.log(tempSaleDetails);
};

const removeProduct = (index) => {
    const tempSaleDetails = [...form.sale_details];
    tempSaleDetails.splice(index, 1);
    form.sale_details = tempSaleDetails;
};

const totalAmount = computed(() => {
    let total = 0;
    form.sale_details.forEach((detail) => {
        let amount = parseFloat(detail.total).toFixed(2);
        total += parseFloat(amount);
    });
    return parseFloat(total.toFixed(2));
});

const totalProducts = computed(() => {
    let total = 0;
    form.sale_details.forEach((detail) => {
        let quantity = parseFloat(detail.quantity).toFixed(2);
        total += parseFloat(quantity);
    });
    return parseFloat(total.toFixed(2));
});

watchEffect(() => {
    form.total_products = totalProducts.value;
    form.total = totalAmount.value;
});

watch(
    () => form.sale_details,
    (newValue, oldValue) => {
        newValue.forEach((detail, index) => {
            const total = detail.quantity * detail.unitcost;
            form.sale_details[index].total = total;
        });
    },
    { deep: true }
);

const formatCurrency = (value) => {
    const decimalCount = (value.toString().split(".")[1] || "").length;
    return accounting.formatMoney(value, {
        symbol: "", // Tidak menampilkan simbol mata uang
        precision: decimalCount || 0, // Menampilkan 2 angka di belakang koma
        thousand: ",", // Menyusun ribuan dengan titik
        decimal: ".", // Menyusun desimal dengan koma
    });
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
                    d.warehouse_id = form.warehouse_id;
                    d.product_ids = form.sale_details.map(
                        (detail) => detail.product_id
                    );
                },
            },
            columns: [
                { data: "name" },
                { data: "unit" },
                { data: "stock" },
                {
                    data: "unitcost",
                    render: function (data, type, row) {
                        return formatCurrency(data);
                    },
                },
            ],
        });

        datatable.on("select", function (e, dt, type, indexes) {
            let rowData = datatable.rows(indexes).data();
            addProduct(rowData[0]);
            modal.hide();
        });

        // setupEventListeners();
    } catch (error) {
        console.error("Gagal memuat data:", error);
    }
};
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

        <form @submit.prevent="submit" autocomplete="off" novalidate>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-3">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">Pengeluaran Baru</h5>
                                <Link
                                    :href="route('sales.index')"
                                    class="btn-close"
                                ></Link>
                            </div>
                        </div>
                        <!--end card-header-->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Tanggal Pengeluaran</h6>
                                    <input
                                        id="date"
                                        class="form-control form-control-lg mb-3"
                                        :class="{
                                            'is-invalid': form.errors.sale_date,
                                        }"
                                        type="text"
                                        v-model="form.sale_date"
                                    />
                                    <div class="invalid-feedback">
                                        {{ form.errors.sale_date }}
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

                            <div v-if="form.warehouse_id">
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <div class="text-end">
                                            <button
                                                type="button"
                                                class="btn btn-de-dashed-primary btn-lg mb-3"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalProducts"
                                            >
                                                <i
                                                    class="fas fa-plus-circle"
                                                ></i>
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
                                                        <th>Stock</th>
                                                        <th>Jumlah</th>
                                                        <th>Harga Acuan</th>
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
                                                        ) in form.sale_details"
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
                                                                        .sale_details[
                                                                        index
                                                                    ].product
                                                                        .name
                                                                "
                                                            />
                                                        </td>
                                                        <td>
                                                            <input
                                                                class="form-control"
                                                                type="text"
                                                                value=""
                                                                disabled
                                                                v-model="
                                                                    form
                                                                        .sale_details[
                                                                        index
                                                                    ].product
                                                                        .stock
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
                                                                                'sale_details.' +
                                                                                    index +
                                                                                    '.quantity'
                                                                            ],
                                                                    }"
                                                                    type="text"
                                                                    v-model="
                                                                        form
                                                                            .sale_details[
                                                                            index
                                                                        ]
                                                                            .quantity
                                                                    "
                                                                />
                                                                <span
                                                                    class="input-group-text"
                                                                    id="basic-addon2"
                                                                    >{{
                                                                        form
                                                                            .sale_details[
                                                                            index
                                                                        ]
                                                                            .product
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
                                                                        form
                                                                            .errors[
                                                                            'sale_details.' +
                                                                                index +
                                                                                '.unitcost'
                                                                        ],
                                                                }"
                                                                type="text"
                                                                v-model="
                                                                    form
                                                                        .sale_details[
                                                                        index
                                                                    ].product
                                                                        .unitcost
                                                                "
                                                                disabled
                                                            />
                                                        </td>
                                                        <td>
                                                            <CurrencyInput
                                                                class="form-control"
                                                                :class="{
                                                                    'is-invalid':
                                                                        form
                                                                            .errors[
                                                                            'sale_details.' +
                                                                                index +
                                                                                '.unitcost'
                                                                        ],
                                                                }"
                                                                type="text"
                                                                v-model="
                                                                    form
                                                                        .sale_details[
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
                                                                        form
                                                                            .errors[
                                                                            'sale_details.' +
                                                                                index +
                                                                                '.total'
                                                                        ],
                                                                }"
                                                                type="text"
                                                                disabled
                                                                v-model="
                                                                    form
                                                                        .sale_details[
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
                                                            colspan="4"
                                                            class="text-end"
                                                        >
                                                            Total Produk
                                                        </th>
                                                        <td>
                                                            <CurrencyInput
                                                                class="form-control"
                                                                :class="{
                                                                    'is-invalid':
                                                                        form
                                                                            .errors
                                                                            .total_products,
                                                                }"
                                                                type="text"
                                                                v-model="
                                                                    form.total_products
                                                                "
                                                                disabled
                                                            />
                                                        </td>

                                                        <td
                                                            class="text-center"
                                                        ></td>
                                                    </tr>
                                                    <tr>
                                                        <th
                                                            colspan="4"
                                                            class="text-end"
                                                        >
                                                            Total Harga
                                                        </th>
                                                        <td>
                                                            <CurrencyInput
                                                                class="form-control"
                                                                :class="{
                                                                    'is-invalid':
                                                                        form
                                                                            .errors
                                                                            .total,
                                                                }"
                                                                type="text"
                                                                v-model="
                                                                    form.total
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
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </form>

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
                                        <th>Harga</th>
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
