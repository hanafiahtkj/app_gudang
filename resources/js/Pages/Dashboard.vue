<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    tot_products: Number,
    tot_purchases: Number,
    tot_sales: Number,
    tot_warehouses: Number,
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
                            <h4 class="page-title pb-md-0">Dashboard</h4>
                            <!-- <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">Dastone</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">Pages</a>
                                </li>
                                <li class="breadcrumb-item active">Starerr</li>
                            </ol> -->
                        </div>
                        <!--end col-->
                        <!-- <div class="col-auto align-self-center">
                                    <a
                                        href="#"
                                        class="btn btn-sm btn-outline-primary"
                                        id="Dash_Date"
                                    >
                                        <span class="day-name" id="Day_Name"
                                            >Today:</span
                                        >&nbsp;
                                        <span class="" id="Select_date"
                                            >Jan 11</span
                                        >
                                        <i
                                            data-feather="calendar"
                                            class="align-self-center icon-xs ms-1"
                                        ></i>
                                    </a>
                                    <a
                                        href="#"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i
                                            data-feather="download"
                                            class="align-self-center icon-xs"
                                        ></i>
                                    </a>
                                </div> -->
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
                <!--end page-title-box-->
            </div>
            <!--end col-->
        </div>
        <!--end row-->
        <div class="row">
            <div class="col-12 col-md-4 col-lg-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="h4 fw-bold">{{
                                    tot_products
                                }}</span>
                                <h6
                                    class="text-uppercase text-muted mt-2 m-0 font-11"
                                >
                                    Produk
                                </h6>
                            </div>
                            <!--end col-->
                            <div class="col-auto">
                                <i
                                    class="lab la-accessible-icon display-3 text-secondary position-absolute o-1 translate-middle"
                                ></i>
                            </div>
                            <!--end col-->
                        </div>
                        <!-- end row -->
                    </div>
                    <!--end card-body-->
                </div>
                <!--end card-->
            </div>
            <!--end col-->
            <div class="col-12 col-md-4 col-lg-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="h4 fw-bold">{{
                                    tot_purchases
                                }}</span>
                                <h6
                                    class="text-uppercase text-muted mt-2 m-0 font-11"
                                >
                                    Pemasukan
                                </h6>
                            </div>
                            <!--end col-->
                            <div class="col-auto position-reletive">
                                <i
                                    class="las la-bed display-3 text-secondary position-absolute o-1 translate-middle"
                                ></i>
                            </div>
                            <!--end col-->
                        </div>
                        <!-- end row -->
                    </div>
                    <!--end card-body-->
                </div>
                <!--end card-->
            </div>
            <!--end col-->
            <div class="col-12 col-md-4 col-lg-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="h4 fw-bold">{{ tot_sales }}</span>
                                <h6
                                    class="text-uppercase text-muted mt-2 m-0 font-11"
                                >
                                    Pengeluaran
                                </h6>
                            </div>
                            <!--end col-->
                            <div class="col-auto">
                                <i
                                    class="las la-cut display-3 text-secondary position-absolute o-1 translate-middle"
                                ></i>
                            </div>
                            <!--end col-->
                        </div>
                        <!-- end row -->
                    </div>
                    <!--end card-body-->
                </div>
                <!--end card-->
            </div>
            <!--end col-->
            <div class="col-12 col-md-4 col-lg-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="h4 fw-bold">{{
                                    tot_warehouses
                                }}</span>
                                <h6
                                    class="text-uppercase text-muted mt-2 m-0 font-11"
                                >
                                    Gudang
                                </h6>
                            </div>
                            <!--end col-->
                            <div class="col-auto">
                                <i
                                    class="las la-stethoscope display-3 text-secondary position-absolute o-1 translate-middle"
                                ></i>
                            </div>
                            <!--end col-->
                        </div>
                        <!-- end row -->
                    </div>
                    <!--end card-body-->
                </div>
                <!--end card-->
            </div>
            <!--end col-->
        </div>
    </AuthenticatedLayout>
</template>
