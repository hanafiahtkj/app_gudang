<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted, watch, computed, watchEffect } from "vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { Modal } from "momentum-modal";
import Swal from "sweetalert2";
import { format } from "date-fns";
import CurrencyInput from "@/Components/CurrencyInput.vue";

const props = defineProps({
    data: {
        type: Object,
    },
    warehouses: {
        type: Object,
    },
    products: {
        type: Object,
    },
});

const form = useForm({
    date: format(props.data.date, "dd/MM/yyyy"),
    warehouse_id: "",
    total_amount: 0,
    sale_details: [],
    ...props.data,
});

const submit = () => {
    form.put(route("stock-reduction.update", { id: props.data.id }), {
        onFinish: () => {
            // form.reset('password', 'password_confirmation');
        },
        onSuccess: () => {
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

const addProduct = (id) => {
    const product = props.products.find((value) => value.id === id);
    const tempSaleDetails = [...form.sale_details];
    tempSaleDetails.push({
        id: "",
        product: product,
        product_id: product.id,
        quantity: 0,
        unitcost: 0,
        total: 0,
    });
    form.sale_details = tempSaleDetails;
    product_id.value = "";
    selectrProduct.deselect();
};

const removeProduct = (index) => {
    const tempSaleDetails = [...form.sale_details];
    tempSaleDetails.splice(index, 1);
    form.sale_details = tempSaleDetails;
};

const totalAmount = computed(() => {
    let total = 0;
    form.sale_details.forEach((detail) => {
        total += parseFloat(detail.total);
    });
    return total;
});

watchEffect(() => {
    form.total_amount = totalAmount.value;
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

onMounted(() => {
    selectrWarehouse = new Selectr("#warehouse");

    selectrProduct = new Selectr("#product");

    var elem = document.querySelector("#date");
    new Datepicker(elem, {
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

        <form @submit.prevent="submit" autocomplete="off" novalidate>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-3">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">Create Sale</h5>
                                <Link
                                    :href="route('stock-reduction.index')"
                                    class="btn-close"
                                ></Link>
                            </div>
                        </div>
                        <!--end card-header-->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Sale Date</h6>
                                    <input
                                        id="date"
                                        class="form-control mb-3"
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
                                    <h6>Warehouse</h6>
                                    <div
                                        class="form-group"
                                        :class="{
                                            'group-invalid-feedback':
                                                form.errors.warehouse_id,
                                        }"
                                    >
                                        <select
                                            id="warehouse"
                                            v-model="form.warehouse_id"
                                        >
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
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6>Product</h6>
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
                                    <!-- end col -->
                                </div>
                                <!-- end row -->

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table
                                                class="table table-bordered mb-0"
                                            >
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Stock</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                        <th>Total</th>
                                                        <th class="text-center">
                                                            Action
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
                                                                        .purchase_details[
                                                                        index
                                                                    ].product
                                                                        .stock
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
                                                                                '.quantity'
                                                                        ],
                                                                }"
                                                                type="text"
                                                                v-model="
                                                                    form
                                                                        .sale_details[
                                                                        index
                                                                    ].quantity
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
                                                            colspan="3"
                                                            class="text-end"
                                                        >
                                                            Total Price
                                                        </th>
                                                        <td>
                                                            <CurrencyInput
                                                                class="form-control"
                                                                :class="{
                                                                    'is-invalid':
                                                                        form
                                                                            .errors
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
                                    Submit
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
