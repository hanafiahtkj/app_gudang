<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, usePage, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const showingNavigationDropdown = ref(false);
let profileModal;

const page = usePage();

const form = useForm({
    name: "",
    email: "",
    posisi: "",
    password: "",
    password_confirmation: "",
    ...page.props.auth.user,
});

const submit = () => {
    form.post(route("myprofile.store"), {
        onFinish: () => {
            // form.reset('password', 'password_confirmation');
        },
        onSuccess: () => {
            Swal.fire({
                title: "Berhasil disimpan!",
                icon: "success",
                showCloseButton: true,
            });
            // profileModal = new bootstrap.Modal(
            //     document.getElementById("profileModal")
            // );
            // profileModal.hide();
        },
    });
};
</script>

<template>
    <!-- leftbar-tab-menu -->

    <!-- Top Bar Start -->
    <div class="topbar">
        <!-- LOGO -->
        <div class="brand">
            <a :href="route('index')" class="logo">
                <span>
                    <img
                        :src="
                            route('index') +
                            '/vendor/metrica/dist/assets/images/logo-sm.png'
                        "
                        alt="logo-small"
                        class="logo-sm"
                    />
                </span>
                <span class="fs-5 fw-bolder" style="color: #c2cbe2">
                    Jack Kolam
                    <!-- <img
                        :src="
                            route('index') +
                            '/vendor/metrica/dist/assets/images/logo.png'
                        "
                        alt="logo-large"
                        class="logo-lg logo-light"
                    />
                    <img
                        :src="
                            route('index') +
                            '/vendor/metrica/dist/assets/images/logo-dark.png'
                        "
                        alt="logo-large"
                        class="logo-lg logo-dark"
                    /> -->
                </span>
            </a>
        </div>
        <!--end logo-->
        <!-- Navbar -->
        <nav class="navbar-custom">
            <ul class="list-unstyled topbar-nav float-end mb-0">
                <li class="dropdown">
                    <a
                        class="nav-link dropdown-toggle nav-user"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button"
                        aria-haspopup="false"
                        aria-expanded="false"
                    >
                        <div class="d-flex align-items-center">
                            <img
                                :src="
                                    route('index') +
                                    '/vendor/metrica/dist/assets/images/users/user-4.jpg'
                                "
                                alt="profile-user"
                                class="rounded-circle me-0 me-md-2 thumb-sm"
                            />
                            <div class="user-name">
                                <small class="d-none d-lg-block font-11"
                                    >Admin</small
                                >
                                <span
                                    class="d-none d-lg-block fw-semibold font-12"
                                    >Super Admin
                                    <i class="mdi mdi-chevron-down"></i
                                ></span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- <a class="dropdown-item" href="#"
                            ><i
                                class="ti ti-user font-16 me-1 align-text-bottom"
                            ></i>
                            Profile</a
                        >
                        <a class="dropdown-item" href="#"
                            ><i
                                class="ti ti-settings font-16 me-1 align-text-bottom"
                            ></i>
                            Settings</a
                        >
                        <div class="dropdown-divider mb-0"></div> -->
                        <a
                            class="dropdown-item"
                            href="#"
                            data-bs-toggle="modal"
                            data-bs-target="#profileModal"
                            ><i
                                class="ti ti-settings font-16 me-1 align-text-bottom"
                            ></i>
                            Profil</a
                        >
                        <Link
                            :href="route('logout')"
                            method="post"
                            class="dropdown-item"
                            ><i
                                class="ti ti-power font-16 me-1 align-text-bottom"
                            ></i>
                            Logout</Link
                        >
                    </div>
                </li>
                <!--end topbar-profile-->
                <li class="menu-item">
                    <!-- Mobile menu toggle-->
                    <a
                        class="navbar-toggle nav-link"
                        id="mobileToggle"
                        onclick="toggleMenu()"
                    >
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div> </a
                    ><!-- End mobile menu toggle-->
                </li>
                <!--end menu item-->
            </ul>
            <!--end topbar-nav-->

            <div class="navbar-custom-menu">
                <div id="navigation">
                    <!-- Navigation Menu-->
                    <ul class="navigation-menu">
                        <li class="nav-item dropdown">
                            <Link
                                class="nav-link"
                                :href="route('dashboard')"
                                id="navbarDashboards"
                            >
                                <span
                                    ><i class="ti ti-smart-home menu-icon"></i
                                    >Dashboard</span
                                >
                            </Link>
                        </li>
                        <!--end nav-item-->

                        <li class="nav-item">
                            <Link
                                :href="route('products.index')"
                                class="nav-link"
                                id="navbarApps"
                            >
                                <span
                                    ><i class="ti ti-apps menu-icon"></i
                                    >Produk</span
                                >
                            </Link>
                        </li>
                        <!--end nav-item-->

                        <li class="nav-item">
                            <Link
                                class="nav-link"
                                :href="route('purchases.index')"
                                id="navbarUI_Kit"
                            >
                                <span
                                    ><i class="ti ti-planet menu-icon"></i
                                    >Pemasukan</span
                                >
                            </Link>
                        </li>
                        <!--end nav-item-->

                        <li class="nav-item">
                            <Link
                                class="nav-link"
                                :href="route('sales.index')"
                                id="navbarPages"
                            >
                                <span
                                    ><i class="ti ti-file-diff menu-icon"></i
                                    >Pengeluaran</span
                                >
                            </Link>
                        </li>
                        <!--end nav-item-->

                        <li class="nav-item">
                            <Link
                                class="nav-link"
                                :href="route('stock-reduction.index')"
                                id="navbarPages"
                            >
                                <span
                                    ><i class="ti ti-file-diff menu-icon"></i
                                    >Penyusutan</span
                                >
                            </Link>
                        </li>
                        <!--end nav-item-->

                        <li class="nav-item">
                            <Link
                                class="nav-link"
                                :href="route('laba.index')"
                                id="navbarPages"
                            >
                                <span
                                    ><i class="ti ti-file-diff menu-icon"></i
                                    >Laba</span
                                >
                            </Link>
                        </li>
                        <!--end nav-item-->

                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                id="navbar_auth"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                <span
                                    ><i class="ti ti-shield-lock menu-icon"></i
                                    >Setup</span
                                >
                            </a>
                            <ul
                                class="dropdown-menu animate slideIn"
                                aria-labelledby="navbar_auth"
                            >
                                <li>
                                    <Link
                                        :href="route('warehouses.index')"
                                        class="dropdown-item"
                                    >
                                        Gudang
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        :href="route('nilai.index')"
                                        class="dropdown-item"
                                    >
                                        Nilai
                                    </Link>
                                </li>
                                <template
                                    v-if="
                                        ['super-admin'].includes(
                                            page.props.auth.user.roles[0].name
                                        )
                                    "
                                >
                                    <li>
                                        <Link
                                            :href="route('users.index')"
                                            class="dropdown-item"
                                        >
                                            User
                                        </Link>
                                    </li>
                                    <li>
                                        <a
                                            :href="
                                                route('index') + '/laratrust'
                                            "
                                            class="dropdown-item"
                                            target="_blank"
                                        >
                                            Role
                                        </a>
                                    </li>
                                </template>
                            </ul>
                            <!--end submenu-->
                        </li>
                        <!--end nav-item-->
                    </ul>
                    <!-- End navigation menu -->
                </div>
                <!-- end navigation -->
            </div>
            <!-- Navbar -->
        </nav>
        <!-- end navbar-->
    </div>
    <!-- Top Bar End -->

    <!-- end leftbar-tab-menu-->

    <div class="page-wrapper">
        <!-- Page Content-->
        <div class="page-content-tab">
            <div class="container-fluid">
                <slot />
                <!-- Page-Title -->

                <!-- end page title end breadcrumb -->
            </div>
            <!-- container -->
        </div>
        <!-- end page content -->
    </div>

    <!--Start Rightbar-->
    <!--Start Rightbar/offcanvas-->
    <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="Appearance"
        aria-labelledby="AppearanceLabel"
    >
        <div class="offcanvas-header border-bottom">
            <h5 class="m-0 font-14" id="AppearanceLabel">Appearance</h5>
            <button
                type="button"
                class="btn-close text-reset p-0 m-0 align-self-center"
                data-bs-dismiss="offcanvas"
                aria-label="Close"
            ></button>
        </div>
        <div class="offcanvas-body">
            <h6>Account Settings</h6>
            <div class="p-2 text-start mt-3">
                <div class="form-check form-switch mb-2">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="settings-switch1"
                    />
                    <label class="form-check-label" for="settings-switch1"
                        >Auto updates</label
                    >
                </div>
                <!--end form-switch-->
                <div class="form-check form-switch mb-2">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="settings-switch2"
                        checked
                    />
                    <label class="form-check-label" for="settings-switch2"
                        >Location Permission</label
                    >
                </div>
                <!--end form-switch-->
                <div class="form-check form-switch">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="settings-switch3"
                    />
                    <label class="form-check-label" for="settings-switch3"
                        >Show offline Contacts</label
                    >
                </div>
                <!--end form-switch-->
            </div>
            <!--end /div-->
            <h6>General Settings</h6>
            <div class="p-2 text-start mt-3">
                <div class="form-check form-switch mb-2">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="settings-switch4"
                    />
                    <label class="form-check-label" for="settings-switch4"
                        >Show me Online</label
                    >
                </div>
                <!--end form-switch-->
                <div class="form-check form-switch mb-2">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="settings-switch5"
                        checked
                    />
                    <label class="form-check-label" for="settings-switch5"
                        >Status visible to all</label
                    >
                </div>
                <!--end form-switch-->
                <div class="form-check form-switch">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="settings-switch6"
                    />
                    <label class="form-check-label" for="settings-switch6"
                        >Notifications Popup</label
                    >
                </div>
                <!--end form-switch-->
            </div>
            <!--end /div-->
        </div>
        <!--end offcanvas-body-->
    </div>
    <!--end Rightbar/offcanvas-->
    <!--end Rightbar-->

    <!--Start Footer-->
    <!-- Footer Start -->
    <footer class="footer text-center text-sm-start">
        &copy; 2024 Jack Kolam
        <!-- <span class="text-muted d-none d-sm-inline-block float-end"
            >Crafted with <i class="mdi mdi-heart text-danger"></i> by
            Mannatthemes</span
        >-->
    </footer>
    <!-- end Footer -->
    <!--end footer-->

    <!-- Modal -->
    <form @submit.prevent="submit" autocomplete="off" novalidate>
        <div
            class="modal fade"
            id="profileModal"
            tabindex="-1"
            aria-labelledby="profileModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="profileModalLabel">
                            Profil
                        </h1>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <h6>Username</h6>
                        <div class="form-group">
                            <input
                                id="email"
                                type="text"
                                placeholder="Username"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.email }"
                                v-model="form.email"
                                autocomplete="off"
                            />
                            <div class="invalid-feedback">
                                {{ form.errors.email }}
                            </div>
                        </div>
                        <h6>Nama Lengkap</h6>
                        <div class="form-group">
                            <input
                                id="name"
                                type="text"
                                placeholder="Name"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.name }"
                                v-model="form.name"
                                autocomplete="off"
                            />
                            <div class="invalid-feedback">
                                {{ form.errors.name }}
                            </div>
                        </div>
                        <h6>Posisi</h6>
                        <div class="form-group">
                            <input
                                id="posisi"
                                type="text"
                                placeholder="Posisi"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.posisi }"
                                v-model="form.posisi"
                                autocomplete="off"
                            />
                            <div class="invalid-feedback">
                                {{ form.errors.posisi }}
                            </div>
                        </div>

                        <h6>Password</h6>
                        <div class="form-group">
                            <input
                                id="password"
                                type="password"
                                placeholder="Password"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.password }"
                                v-model="form.password"
                                autocomplete="off"
                            />
                            <div class="invalid-feedback">
                                {{ form.errors.password }}
                            </div>
                        </div>
                        <h6>Konfirmasi Password</h6>
                        <div class="form-group">
                            <input
                                id="confirm-password"
                                type="password"
                                placeholder="Password"
                                class="form-control"
                                v-model="form.password_confirmation"
                                autocomplete="off"
                            />
                        </div>
                        <div class="alert alert-outline-warning mt-3">
                            Untuk mengubah Password silahkan isi Password yang
                            terbaru
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
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
