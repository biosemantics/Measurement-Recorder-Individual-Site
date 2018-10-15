/**
 * Created by ZEUS on 5/22/2018.
 */
import Specimen1 from './components/Pages/speciment1.vue';
import Specimen2 from './components/Pages/speciment2.vue';
import Specimen3 from './components/Pages/speciment3.vue';

const routes = [
    {
        path: '/mr/individual/public/home',
        component: Specimen1,
        name: 'specimen-1'
    },
    {
        path: '/specimen-2',
        component: Specimen2,
        name: 'specimen-2'
    },
    {
        path: '/specimen-3',
        component: Specimen3,
        name: 'specimen-3'
    },

];

export default routes;