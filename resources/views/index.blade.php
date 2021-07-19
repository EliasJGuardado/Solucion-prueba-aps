@extends('layouts.app')
@section('contenido')
<v-app>
<template v-if="menu==0">
  <example-component></example-component>
  </template>
  <template v-if="menu==1">
  <Asociados></Asociados>
  </template>
  </v-app>
@endsection