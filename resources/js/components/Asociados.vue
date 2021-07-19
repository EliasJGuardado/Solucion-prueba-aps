<template>
  <div class="content">
    <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
      <v-overlay :value="loader" :z-index="'99999999'">
        <v-progress-circular indeterminate size="80" color="grey darken-4"></v-progress-circular>
      </v-overlay>
      <v-card>
        <v-card-title>
          Listado de Asociados
          <div class="flex-grow-1"></div>
          <v-text-field v-model="searchAsociados" label="Buscar Asociados" hide-details></v-text-field>
        </v-card-title>
        <v-data-table
          :headers="hTBAsociados"
          :items="arrayAsociados"
          :footer-props="{
            'items-per-page-options': [5,10, 20, 30,40],
            'items-per-page-text' : 'Registros Por Página'
          }"
          :items-per-page="5"
          :search="searchAsociados"
          multi-sort
          class="elevation-1"
        >
          <template v-slot:top>
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="modalAsociado" persistent max-width="700px">
                <template v-slot:activator="{ on }">
                  <v-btn elevation="10" color="grey darken-3" dark class="mb-2" v-on="on">
                    Agregar&nbsp;
                    <v-icon>mdi-plus-box-multiple-outline</v-icon>
                  </v-btn>
                </template>
                <v-card>
                  <v-card-title class="headline grey lighten-2" primary-titles>
                    <span class="headline" v-text="formTitle"></span>
                  </v-card-title>
                  <v-card-text>
                    <v-container>
                      <v-form ref="formAsociado" v-model="validForm" :lazy-validation="true">
                        <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="asociado.nombre"
                          @keyup="errorsNombre = []"
                          :rules="[v => !!v || 'Nombre Es Requerido']"
                          label="Nombre"
                          required
                          :error-messages="errorsNombre"
                        ></v-text-field>
                         <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="asociado.dui"
                          @keyup="errorsDui = []"
                          :rules="[v => !!v || 'Dui Es Requerido']"
                          label="dui"
                          required
                          :error-messages="errorsDui"
                        ></v-text-field>
                        <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="asociado.nit"
                          @keyup="errorsNit = []"
                          :rules="[v => !!v || 'Nit Es Requerido']"
                          label="nit"
                          required
                          :error-messages="errorsNit"
                        ></v-text-field>
                        <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="asociado.direccion"
                          @keyup="errorsDireccion = []"
                          :rules="[v => !!v || 'Direccion Es Requerido']"
                          label="direccion"
                          required
                          :error-messages="errorsDireccion"
                        ></v-text-field>
                        
                      </v-form>
                    </v-container>
                  </v-card-text>
                  <v-divider></v-divider>
                  <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="red darken-1" text @click="cerrarModal">Cerrar</v-btn>
                    <v-btn
                      color="info darken-1"
                      :disabled="!validForm"
                      @click="saveAsociado()"
                      text
                      v-text="btnTitle"
                    ></v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </v-toolbar>
          </template>

           <!-- template para mostrar si activo o inactivo -->
          <template v-slot:item.estado="{ item }">{{
            item.estado == "1" ? "Activo" : "Inactivo"
          }}</template>  
          <!-- Template que va en la tabla en la columna de Acciones (Editar,Desactivar) -->

          <template v-slot:item.action="{item}" v-slot:activator="{ on }">
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                  color="success"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="showModalEditar(item)"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>
              <span>Actualizar Datos</span>
            </v-tooltip>
            <v-tooltip top v-if="item.estado=='1'">
              <template v-slot:activator="{ on }" >
                <v-btn
                  color="info"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="deleteAsociado(item)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Desactivar Registro</span>
            </v-tooltip>
            <v-tooltip top v-if="item.estado=='0'">
              <template v-slot:activator="{ on }" >
                <v-btn
                  color="info"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="activarAsociado(item)"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
              </template>
              <span>Activar Registro</span>
            </v-tooltip>
          </template>
        </v-data-table>
        <v-snackbar v-model="snackbar">
          {{ msjSnackBar }}
          <v-btn color="red" text @click="snackbar = false">Cerrar</v-btn>
        </v-snackbar>
      </v-card>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      arrayAsociados: [],
      hTBAsociados: [
        { text: "Nombre", value: "nombre" },
        { text: "Dui", value: "dui" },
        { text: "Nit", value: "nit" },
        { text: "Direccion", value: "direccion" },
        { text: "Estado", value: "estado" },
        { text: "Acciones", value: "action", sortable: false, align: "center" }
      ],
      loader: false,
      searchAsociados: "",
      modalAsociado: false,
      asociado: {
        id: null,
        nombre: "",
        dui: "",
        nit: "",
        direccion: "",
        estado:1
      },
      validForm: true,
      snackbar: false,
      msjSnackBar: "",
      errorsNombre: [],
      errorsDui: [],
      errorsNit: [],
      errorsDireccion: [],
      editedAsociado: -1
    };
  },
  computed: {
    formTitle() {
      return this.asociado.id === null
        ? "Agregar Asociado"
        : "Actualizar Asociado";
    },
    btnTitle() {
      return this.asociado.id === null ? "Guardar" : "Actualizar";
    }
  },
  methods: {
    fetchAsociados() {
      let me = this;
      me.loader = true;
      
      axios.get('/asociados')
        .then(function(response) {
          me.arrayAsociados = response.data;
          me.loader = false;
        })
        .catch(function(error) {
          me.loader = false;
          console.log(error);
        });
    },
    setMessageToSnackBar(msj, estado) {
      let me = this;
      me.snackbar = estado;
      me.msjSnackBar = msj;
    },
    cerrarModal() {
      let me = this;
      me.modalAsociado = false;
      setTimeout(() => {
        me.asociado = {
          id: null,
          nombre: "",
          dui: "",
          nit: "",
          direccion: "",
          estado:1
        };
        me.resetValidation();
      }, 300);
    },
    resetValidation() {
      let me = this;
      me.errorsNombre = [];
      me.$refs.formAsociado.resetValidation();
    },
    showModalEditar(asociado) {
      let me = this;
      me.editedAsociado = me.arrayAsociados.indexOf(asociado);
      me.asociado = Object.assign({}, asociado);
      me.modalAsociado = true;
    },
    saveAsociado() {
      let me = this;
      if (me.$refs.formAsociado.validate()) {
        let accion = me.asociado.id == null ? "add" : "upd";
        me.loader = true;
        if(accion=="add"){
           axios.post('/asociados/save', me.asociado)
            .then(function(response) {
            me.verificarAccionDato(response.data, response.status, accion);
            me.cerrarModal();
          })
          .catch(function(error) {
            console.log(error);
            //409 Conflicts Error (Proveedor Ya Existente En la BD)
            if (error.response.status == 409) {
              me.setMessageToSnackBar("Asociado Ya Existe", true);
              me.errorsNombre = ["Nombre De Asociado Existente"];
              me.errorsDui = ["Dui De Asociado Existente"];
              me.errorsNit = ["Nit De Asociado Existente"];
              me.errorsDireccion = ["Direccion De Asociado Existente"];
            } else {
              me.$swal("Error", "Ocurrio Un Error Intente Nuevamente", "error");
            }
            me.loader = false;
          });
        }else{
            //para actualizar
            axios.put('/asociados/update', me.asociado)
               .then(function(response) {
                   //console.log(response.data);
                    me.verificarAccionDato(response.data, response.status, accion);
                    me.cerrarModal();
            })
          .catch(function(error) {
            console.log(error);
            me.loader = false;
          });
        }
      
      }
    },
    deleteAsociado(asociado) {
      let me = this;
      me.editedAsociado = me.arrayAsociados.indexOf(asociado);
      
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
        });
        //personalizando nueva confirmacion
        Swal.fire({
        title: 'Desactivar Asociado',
        text: "Una vez realizada la acción no se podra revertir !",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: "No"
        }).then((result) => {
        if (result.value) {
            me.loader = true;
            axios.put(`/asociados/desactivar`, asociado)
            .then(function(response) {
              me.verificarAccionDato(response.data, response.status, "del");
              me.loader = false;
            })
          }
        });
    },
    activarAsociado(asociado) {
      let me = this;
      me.editedAsociado = me.arrayAsociados.indexOf(asociado);
      
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
        });
        //personalizando nueva confirmacion
        Swal.fire({
        title: 'Activar Asociado',
        text: "Una vez realizada la acción no se podra revertir !",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: "No"
        }).then((result) => {
        if (result.value) {
            me.loader = true;
            axios.put(`/asociados/activar`, asociado)
            .then(function(response) {
              me.verificarAccionDato(response.data, response.status, "act");
              me.loader = false;
            })
          }
        });
    },
    verificarAccionDato(asociado, statusCode, accion) {
      let me = this;
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
        });
      switch (accion) {
        case "add":
          //Agrego al array de asociado el objecto que devuelve el Backend
          //me.arrayAsociados.unshift(asociado);
          this.fetchAsociados(); 
          Toast.fire({
            icon: 'success',
            title: 'asociado Registrada con Exito'
           });
          me.loader = false;
          break;
        case "upd":
          //Actualizo al array de asociados el objecto que devuelve el Backend ya con los datos actualizados
          //Object.assign(me.arrayAsociados[me.editedAsociados], asociado);
          this.fetchAsociados(); 
           Toast.fire({
            icon: 'success',
            title: 'Asociado Actualizada con Exito'
           });
          me.loader = false;
          break;
        case "del":
          if (statusCode == 200) {
            try {
              //Se elimina del array de Asociados Activos si todo esta bien en el backend
              //me.arrayAsociados.splice(me.editedAsociado, 1);
              me.arrayAsociados[me.editedAsociado].estado = '0';
              //Se Lanza mensaje Final
              Toast.fire({
                icon: 'success',
                title: 'Asociado Desactivada'
              });
            } catch (error) {
              console.log(error);
            }
          } else {
             Toast.fire({
                icon: 'error',
                title: 'Ocurrió un error, intente de nuevo'
              });
          }
          break;
      case "act":
          if (statusCode == 200) {
            try {
              me.arrayAsociados[me.editedAsociado].estado = '1';
              //Se Lanza mensaje Final
              Toast.fire({
                icon: 'success',
                title: 'Asociado Activada'
              });
            } catch (error) {
              console.log(error);
            }
          } else {
             Toast.fire({
                icon: 'error',
                title: 'Ocurrió un error, intente de nuevo'
              });
          }
        break;    
      }
    }
  },
  mounted() {
    let me = this;
    me.fetchAsociados();
  }
};
</script>