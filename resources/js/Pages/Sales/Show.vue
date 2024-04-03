<script setup>
import Swal from "sweetalert2";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useModal } from "momentum-modal";
import { Head, Link, useForm, router, usePage } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import accounting from "accounting";

const { show, close, redirect } = useModal();

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
    redrawDataTable: {
        type: Function,
    },
});

let modal;
let events;

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
    modal = new bootstrap.Modal(document.getElementById("inlineForm"));
    modal.show();

    events = document.getElementById("inlineForm");
    events.addEventListener("hidden.bs.modal", redirect);
});
</script>

<template>
    <form autocomplete="off" novalidate>
        <div
            class="modal fade text-left"
            id="inlineForm"
            tabindex="-1"
            aria-labelledby="myModalLabel33"
            aria-hidden="true"
            style="display: none"
        >
            <div
                class="modal-dialog modal-fullscreen modal-dialog-scrollable"
                role="document"
            >
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title" id="myModalLabel33">
                            Detail Pengeluaran
                        </h4>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <!--end card-header-->
                            <div class="card-body">
                                <div
                                    class="row row-cols-6 d-flex justify-content-md-between"
                                >
                                    <div class="col-md-3 d-print-flex">
                                        <div class="">
                                            <h6 class="mb-0">
                                                <b>Tanggal Pengeluaran :</b>
                                                {{ data.sale_date }}
                                            </h6>
                                        </div>
                                    </div>
                                    <!--end col-->

                                    <!--end col-->
                                    <div class="col-md-6 d-print-flex">
                                        <div class="">
                                            <address class="font-13">
                                                <strong class="font-14"
                                                    >Gudang:
                                                </strong>
                                                {{ data.warehouse.name }}<br />
                                                {{ data.warehouse.address }}
                                            </address>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Nama Produk</th>
                                                        <th>Jumlah</th>
                                                        <th>Harga</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr
                                                        v-for="(
                                                            details, index
                                                        ) in data.sale_details"
                                                        :key="index"
                                                    >
                                                        <td>
                                                            {{
                                                                data
                                                                    .sale_details[
                                                                    index
                                                                ].product.name
                                                            }}
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="input-group"
                                                            >
                                                                {{
                                                                    data
                                                                        .sale_details[
                                                                        index
                                                                    ].quantity
                                                                }}
                                                                {{
                                                                    data
                                                                        .sale_details[
                                                                        index
                                                                    ].product
                                                                        .unit
                                                                }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            {{
                                                                formatCurrency(
                                                                    data
                                                                        .sale_details[
                                                                        index
                                                                    ].unitcost
                                                                )
                                                            }}
                                                        </td>
                                                        <td>
                                                            {{
                                                                formatCurrency(
                                                                    data
                                                                        .sale_details[
                                                                        index
                                                                    ].total
                                                                )
                                                            }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <!--end /table-->
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 offset-md-6">
                                        <div class="total-payment p-3 mt-0">
                                            <h4 class="header-title">Total</h4>
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td
                                                            class="fw-semibold ps-0"
                                                        >
                                                            <b>Total Produk</b>
                                                        </td>
                                                        <td>
                                                            <b>{{
                                                                data.total_products
                                                            }}</b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            class="border-bottom-0 ps-0"
                                                        >
                                                            <b>Total Harga</b>
                                                        </td>
                                                        <td
                                                            class="text-dark border-bottom-0"
                                                        >
                                                            <strong>{{
                                                                formatCurrency(
                                                                    data.total
                                                                )
                                                            }}</strong>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                        <!-- <button
                            type="submit"
                            class="btn btn-primary ms-1"
                            :disabled="form.processing"
                        >
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Simpan</span>
                        </button> -->
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
