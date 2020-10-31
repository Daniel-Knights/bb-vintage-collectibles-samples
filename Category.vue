<template>
    <div class="home-item-container">
        <div
            class="items-container"
            v-for="category in $randomize(Object.keys(filtered))"
            :key="category"
        >
            <router-link
                :to="{
                    name: 'Category',
                    query: { category }
                }"
                class="category-names-home-link"
            >
                <h2 class="category-names-home">
                    {{ $capitalize(category) }}
                </h2>
            </router-link>
            <router-link
                v-if="filtered[category][1]"
                :to="{
                    name: 'Category',
                    query: { category }
                }"
                class="home-view-all"
            >
                <p>
                    View All...
                </p>
            </router-link>
            <div class="items">
                <SubItem :item="filtered[category][0]" />
                <SubItem class="second-item" :item="filtered[category][1]" />
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

import SubItem from "../components/items/SubItem";

export default {
    name: "Category",

    components: { SubItem },

    computed: {
        ...mapGetters(["allItems"]),
        // Sort items into categories
        filtered: function() {
            if (!this.allItems) return null;

            const filtered = {};

            this.allItems.forEach(item => {
                if (item.hide_show !== 2) return;

                if (filtered[item.category]) {
                    filtered[item.category].push(item);
                } else filtered[item.category] = [item];
            });

            return filtered;
        }
    }
};
</script>
