<template>
    <div>
        <Modal />
        <div class="single-item-container">
            <div class="single-item-header">
                <div class="single-item-title">
                    <h1>
                        {{ item[0].name }}
                    </h1>
                </div>
                <div class="single-item-slash">/</div>
                <div class="single-item-category">
                    <router-link
                        :to="{
                            name: 'Category',
                            query: {
                                category: item[0].category
                            }
                        }"
                    >
                        <h3>
                            {{ $route.query.category.toUpperCase() }}
                        </h3>
                    </router-link>
                </div>
            </div>
            <div class="single-item">
                <!-- Image section component -->
                <ItemImage :item="item[0]" />
                <div class="single-item-info">
                    <div class="single-item-name">
                        <h3>
                            {{ item[0].name }}
                        </h3>
                        <h3 v-if="all.length === 0">SOLD</h3>
                    </div>
                    <div class="single-item-info-category">
                        <router-link
                            :to="{
                                name: 'Category',
                                query: {
                                    category: item[0].category
                                }
                            }"
                        >
                            <h3>
                                {{ $route.query.category.toUpperCase() }}
                            </h3>
                        </router-link>
                        <h3 id="single-item-price">
                            {{ item[0].price }}
                        </h3>
                    </div>
                    <div class="single-item-description">
                        <p>
                            <span v-html="item[0].description"></span>
                        </p>
                    </div>
                    <!-- Interactive button section component -->
                    <ItemBtns
                        v-if="all.length !== 0 && item[0].hide_show !== 1"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

import ItemImage from "../components/items/ItemImage";
import ItemBtns from "../components/items/ItemBtns";

export default {
    name: "item",

    metaInfo() {
        return {
            title: this.item[0].name
        };
    },

    components: {
        Modal: () =>
            import(/* webpackChunkName: 'modal' */ "../components/Modal"),
        ItemImage,
        ItemBtns
    },

    data() {
        return {
            filteredItem: null
        };
    },

    computed: {
        ...mapGetters(["allItems", "getDeletedItem"]),
        item: function() {
            return this.filteredItem.length > 0
                ? this.filteredItem
                : [this.getDeletedItem];
        }
    },

    methods: {
        ...mapActions(["fetchDeletedItem"])
    },

    created() {
        let itemId = this.$route.query.id;

        this.filteredItem = this.allItems.filter(
            item => item.id === Number(itemId)
        );

        // Determine if item has been deleted
        if (this.allItems.length !== 0 && !this.filteredItem) {
            this.fetchDeletedItem(itemId);
        }
    }
};
</script>
