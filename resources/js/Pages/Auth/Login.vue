<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    username: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <!-- <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form> -->

        <div
            class="auth-page"
            style="
                background-image: url('/vendor/metrica/dist/assets/images/p-1.png');
                background-size: cover;
                background-position: center center;
            "
        >
            <div class="container-md">
                <div class="row vh-100 d-flex justify-content-center">
                    <div class="col-12 align-self-center">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 mx-auto">
                                    <div class="card">
                                        <div
                                            class="card-body p-0 auth-header-box"
                                        >
                                            <div class="text-center p-3">
                                                <a
                                                    :href="route('index')"
                                                    class="logo logo-admin"
                                                >
                                                    <img
                                                        :src="
                                                            route('index') +
                                                            '/vendor/metrica/dist/assets/images/logo-sm.png'
                                                        "
                                                        height="50"
                                                        alt="logo"
                                                        class="auth-logo"
                                                    />
                                                </a>
                                                <h4
                                                    class="mt-3 mb-1 fw-semibold text-white font-18"
                                                >
                                                    Aplikasi Inventaris
                                                </h4>
                                                <p class="text-muted mb-0">
                                                    Masuk untuk melanjutkan ke
                                                    Aplikasi Inventaris.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="card-body py-0">
                                            <div
                                                v-if="status"
                                                class="mb-4 font-medium text-sm text-green-600"
                                            >
                                                {{ status }}
                                            </div>
                                            <form
                                                class="my-4"
                                                @submit.prevent="submit"
                                            >
                                                <div class="form-group mb-2">
                                                    <label
                                                        class="form-label"
                                                        for="username"
                                                        >Username</label
                                                    >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="username"
                                                        name="username"
                                                        placeholder=""
                                                        v-model="form.username"
                                                        required
                                                        autofocus
                                                        autocomplete="username"
                                                        :class="{
                                                            'is-invalid':
                                                                form.errors
                                                                    .username,
                                                        }"
                                                    />
                                                    <div
                                                        class="invalid-feedback"
                                                    >
                                                        {{
                                                            form.errors.username
                                                        }}
                                                    </div>
                                                </div>
                                                <!--end form-group-->

                                                <div class="form-group">
                                                    <label
                                                        class="form-label"
                                                        for="userpassword"
                                                        >Password</label
                                                    >
                                                    <input
                                                        type="password"
                                                        class="form-control"
                                                        name="password"
                                                        id="userpassword"
                                                        placeholder=""
                                                        v-model="form.password"
                                                        required
                                                        autocomplete="current-password"
                                                        :class="{
                                                            'is-invalid':
                                                                form.errors
                                                                    .password,
                                                        }"
                                                    />
                                                    <div
                                                        class="invalid-feedback"
                                                    >
                                                        {{
                                                            form.errors.password
                                                        }}
                                                    </div>
                                                </div>
                                                <!--end form-group-->

                                                <div
                                                    class="form-group row mt-3"
                                                >
                                                    <div class="col-sm-6">
                                                        <div
                                                            class="form-check form-switch form-switch-success"
                                                        >
                                                            <Checkbox
                                                                id="customSwitchSuccess"
                                                                class="form-check-input"
                                                                name="remember"
                                                                v-model:checked="
                                                                    form.remember
                                                                "
                                                            />
                                                            <label
                                                                class="form-check-label"
                                                                for="customSwitchSuccess"
                                                                >Remember
                                                                me</label
                                                            >
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <!-- <div class="col-sm-6 text-end">
                                                    <a
                                                        href="auth-recover-pw.html"
                                                        class="text-muted font-13"
                                                        ><i
                                                            class="dripicons-lock"
                                                        ></i>
                                                        Forgot password?</a
                                                    >
                                                </div> -->
                                                    <!--end col-->
                                                </div>
                                                <!--end form-group-->

                                                <div
                                                    class="form-group mb-0 row"
                                                >
                                                    <div class="col-12">
                                                        <div
                                                            class="d-grid mt-3"
                                                        >
                                                            <button
                                                                class="btn btn-primary"
                                                                type="submit"
                                                                :disabled="
                                                                    form.processing
                                                                "
                                                            >
                                                                Log In
                                                                <i
                                                                    class="fas fa-sign-in-alt ms-1"
                                                                ></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end form-group-->
                                            </form>
                                            <!--end form-->
                                            <!-- <div class="m-3 text-center text-muted">
                                            <p class="mb-0">
                                                Don't have an account ?
                                                <a
                                                    href="auth-register.html"
                                                    class="text-primary ms-2"
                                                    >Free Resister</a
                                                >
                                            </p>
                                        </div>
                                        <hr class="hr-dashed mt-4" />
                                        <div class="text-center mt-n5">
                                            <h6
                                                class="card-bg px-3 my-4 d-inline-block"
                                            >
                                                Or Login With
                                            </h6>
                                        </div>
                                        <div
                                            class="d-flex justify-content-center mb-1"
                                        >
                                            <a
                                                href=""
                                                class="d-flex justify-content-center align-items-center thumb-sm bg-soft-primary rounded-circle me-2"
                                            >
                                                <i
                                                    class="fab fa-facebook align-self-center"
                                                ></i>
                                            </a>
                                            <a
                                                href=""
                                                class="d-flex justify-content-center align-items-center thumb-sm bg-soft-info rounded-circle me-2"
                                            >
                                                <i
                                                    class="fab fa-twitter align-self-center"
                                                ></i>
                                            </a>
                                            <a
                                                href=""
                                                class="d-flex justify-content-center align-items-center thumb-sm bg-soft-danger rounded-circle"
                                            >
                                                <i
                                                    class="fab fa-google align-self-center"
                                                ></i>
                                            </a>
                                        </div> -->
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                    <!--end card-->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                        <!--end card-body-->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div>
            <!--end container-->
            <!-- vendor js -->
        </div>
    </GuestLayout>
</template>
