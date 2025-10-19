// Alpine.js component for areas management
document.addEventListener('alpine:init', () => {
    Alpine.data('areasManagement', () => ({
        isCreateModalOpen: false,
        isEditModalOpen: false,
        currentArea: { id: null, name: '', description: '' },
        validationErrors: {},
        createForm: { name: '', description: '' },

        init() {
            // Check for validation errors from server
            if (window.areaErrors && Object.keys(window.areaErrors).length > 0) {
                this.isCreateModalOpen = true;
                this.validationErrors = window.areaErrors;
                this.createForm.name = window.oldName || '';
                this.createForm.description = window.oldDescription || '';
            }
        },

        openEditModal(id, name, description) {
            this.isEditModalOpen = true;
            this.currentArea.id = id;
            this.currentArea.name = name;
            this.currentArea.description = description;
            this.validationErrors = {};
        },

        resetCreateForm() {
            this.createForm.name = '';
            this.createForm.description = '';
            this.validationErrors = {};
        },

        async createArea() {
            try {
                const formData = new FormData(this.$refs.createForm);
                const response = await fetch('/infrastock/admin/areas', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (response.ok) {
                    this.isCreateModalOpen = false;
                    this.resetCreateForm();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Área creada correctamente.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else if (response.status === 422) {
                    const errorData = await response.json();
                    this.validationErrors = errorData.errors;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al crear el área.'
                    });
                }
            } catch (error) {
                console.error('Error al enviar el formulario:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo conectar con el servidor.'
                });
            }
        },

        async updateArea() {
            try {
                const formData = new FormData(this.$refs.editForm);
                formData.append('_method', 'PUT');
                const response = await fetch('/infrastock/admin/areas/' + this.currentArea.id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (response.ok) {
                    this.isEditModalOpen = false;
                    this.validationErrors = {};
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Área actualizada correctamente.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else if (response.status === 422) {
                    const errorData = await response.json();
                    this.validationErrors = errorData.errors;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al actualizar el área.'
                    });
                }
            } catch (error) {
                console.error('Error al enviar el formulario:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo conectar con el servidor.'
                });
            }
        }
    }));
});
