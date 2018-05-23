<template>
    <ul class="nav nav-tabs ">
        <router-link class="active" :to="{ name: 'specimen-1' }" tag="li">
            <a data-toggle="tab" style="cursor: pointer;">Specimen 1</a>
        </router-link>
        <router-link class="active" :to="{ name: 'specimen-2' }" tag="li">
            <a data-toggle="tab" style="cursor: pointer;">Specimen 2</a>
        </router-link>
        <router-link class="active" :to="{ name: 'specimen-3' }" tag="li">
            <a data-toggle="tab" style="cursor: pointer;">Specimen 3</a>
        </router-link>
    </ul>
</template>
