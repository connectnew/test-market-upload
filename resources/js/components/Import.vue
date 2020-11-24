<template>
    <div>
        <div class="py-5 text-center">
            <i class="fa fa-shopping-basket fa-3x" aria-hidden="true"></i>
            <h2>Import goods form</h2>
            <p class="lead">Please upload xlsx file in the form.</p>
        </div>
        <SuccessError
            :show="{ not_success, success }"
            :fnHideMessage="hideResultMessage"
        />
        <div class="row mt-3">
            <div class="col-md-4 order-md-2 mb-4">
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                            <h6 class="my-0">Total rows</h6>
                            <small class="text-muted"></small>
                        </div>
                        <span class="text-muted">{{ uploadFile.total }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                            <h6 class="my-0">Created or Updated rows</h6>
                            <small class="text-muted"></small>
                        </div>
                        <span class="text-muted">{{ resultParse.rows }}</span>
                    </li>
                </ul>
            </div>
            <div class="col-md-8 order-md-1">
                <form class="">
                    <div class="mb-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                </span>
                            </div>
                            <input
                                :class="('file' in invalid) ? 'is-invalid' : ''"
                                ref="fileXlsx"
                                type="file"
                                :value="fileXlsx"
                            >
                            <div
                                v-if="'file' in invalid"
                                class="invalid-feedback"
                            >
                                <span
                                    v-for="(message, k) in invalid.file"
                                    :key="k"
                                >{{ message }}</span>
                            </div>
                        </div>
                    </div>
                    <button
                        class="btn btn-primary"
                        type="button"
                        @click="handleFileUpload()"
                        :disabled="spinner.save"
                    >
                        Upload
                        <span
                            v-if="spinner.save"
                            class="spinner-border spinner-border-sm"
                            role="status"
                            aria-hidden="true"
                        />
                    </button>
                </form>
            </div>
        </div>
        <div class="row mt-3">
            <table class="table" v-if="resultParse.rowErrors.length">
                <thead>
                    <tr>
                        <th scope="col" width="10%">Str Number</th>
                        <th scope="col">Error</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, k) in resultParse.rowErrors" :key="k">
                        <td>{{ item.number }}</td>
                        <td>{{ item.message }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>

import SuccessError from "./inc/message/SuccessError";
import Proccess from "./inc/mixin/Proccess";

export default {
    name: "Import",
    components: {
        SuccessError
    },
    mixins: [Proccess],
    data() {
        return {
            file: null,
            fileXlsx: null,
            uploadFile: {
                total: 0,
                name: '',
                folder: '',
            },
            resultParse: {
                rows: 0,
                rowErrors: [],
            },
        }
    },
    mounted() {
    },
    methods: {
        async upload() {
            const url = '/api/v1/import/upload';
            const btn = 'save';
            const notify = true;
            const loading = 'local';

            const formData = new FormData();
            formData.append('file', this.file);

            this.showProcessMessage(loading, btn);

            try {

                const response = await axios.post(url, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                const { data: { file: { name, folder, total } } } = response;

                this.showSuccessMessage(loading, btn, notify);

                this.uploadFile.name = name;
                this.uploadFile.folder = folder;
                this.uploadFile.total = parseInt(total);

                this.fileXlsx = null;
                this.file = null;

                if (this.uploadFile.total) {
                    setTimeout(() => { this.parse(1, 50) }, 0);
                }

            } catch (error) {
                this.showErrorMessage(loading, btn, notify, error);
            }
        },
        async parse(start, end) {
            const url = '/api/v1/import/parse';
            const btn = 'save';
            const notify = false;
            const loading = 'local';

            const formData = new FormData();
            formData.append('folder', this.uploadFile.folder);
            formData.append('start', start);
            formData.append('end', end);

            this.showProcessMessage(loading, btn);

            try {

                const response = await axios.post(url, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                const { data: { result : { rows, rowErrors } } } = response;

                this.showSuccessMessage(loading, btn, notify);

                this.resultParse.rows += parseInt(rows);

                if (rowErrors.length) {
                    this.resultParse.rowErrors = this.resultParse.rowErrors.concat(rowErrors);
                }

                const step = 50;

                if (this.uploadFile.total > (end + step)) {
                    start += step;
                    end += step;
                } else {
                    start += step;
                    end = this.uploadFile.total;
                }

                if (start <= end) {
                    setTimeout(() => { this.parse(start, end); }, 0);
                } else {
                    this.showSuccessMessage(loading, '', true);
                }

            } catch (error) {
                this.showErrorMessage(loading, btn, notify, error);
            }
        },
        handleFileUpload() {
            [this.file] = this.$refs.fileXlsx.files;
            if (this.file) {
                this.upload();
            }
        },
    },
}
</script>

<style scoped>

</style>
