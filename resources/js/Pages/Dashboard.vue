<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    today: String,
    tot_products: Number,
    tot_purchases: Number,
    tot_sales: Number,
    tot_warehouses: Number,
    tot_stock_reductions: Number,
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
                        <div class="col-auto align-self-center">
                            <h5 href="#" class="" id="Dash_Date">
                                <span class="day-name" id="Day_Name"
                                    >Hari ini :</span
                                >&nbsp;
                                <span class="" id="Select_date">{{
                                    today
                                }}</span>
                                <i
                                    data-feather="calendar"
                                    class="align-self-center icon-xs ms-1"
                                ></i>
                            </h5>
                            <!-- <a href="#" class="btn btn-sm btn-outline-primary">
                                <i
                                    data-feather="download"
                                    class="align-self-center icon-xs"
                                ></i>
                            </a> -->
                        </div>
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
            <!--end col-->
            <div class="col-12 col-md-4 col-lg-4">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="h4 fw-bold"
                                    >{{ tot_purchases }} Produk</span
                                >
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
            <div class="col-12 col-md-4 col-lg-4">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="h4 fw-bold"
                                    >{{ tot_sales }} Produk</span
                                >
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
            <div class="col-12 col-md-4 col-lg-4">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="h4 fw-bold"
                                    >{{ tot_stock_reductions }} Produk</span
                                >
                                <h6
                                    class="text-uppercase text-muted mt-2 m-0 font-11"
                                >
                                    Penyusutan
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
