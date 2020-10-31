import axios from "axios";

const actions = {
    // Fetch all items from database
    async fetchItems({ commit }) {
        await axios.get("items").then(res => {
            commit("setItems", res.data);
        });
    },

    async addItem({ commit, dispatch }, item) {
        item.images = item.images.sort();

        await axios
            .post("create/item", item)
            .then(() => {
                let message =
                    "<strong class='alert-success'>Item Created</strong>";

                commit("setItemMessage", message);
                commit("setItemStatus", true);
                dispatch("fetchItems");
            })
            .catch(() => {
                let message =
                    "<strong class='alert-danger'>Unable To Add Item</strong>";

                commit("setItemMessage", message);
                commit("setItemStatus", false);
            });
    },

    async updateItem({ commit }, item) {
        await axios
            .post(`update/item/${item.id}`, item)
            .then(() => {
                let message =
                    "<strong class='alert-success'>Item Updated</strong>";

                commit("setItemMessage", message);
            })
            .catch(() => {
                let message =
                    "<strong class='alert-danger'>Unable To Update Item</strong>";

                commit("setItemMessage", message);
            });
    },

    async deleteItem({ commit, dispatch }, id) {
        await axios
            .delete(`items/${id}`)
            .then(() => {
                let message =
                    "<strong class='alert-success'>Item Deleted</strong>";

                commit("setItemMessage", message);
                dispatch("fetchItems");
            })
            .catch(() => {
                let message =
                    "<strong class='alert-danger'>Unable To Delete Item</strong>";

                commit("setItemMessage", message);
            });
    },

    async deleteWatchedItems({ dispatch }, id) {
        await axios.delete(`delete/watched/${id}`).then(dispatch("fetchItems"));
    },

    // Set single item
    itemProps({ commit }, item) {
        commit("addItemProp", item);
    },
    // Set modal image
    changeMainImage({ commit }, image) {
        commit("mainImageSrc", image);
    },

    // Error/success messages
    clearItemMessage({ commit }) {
        commit("setItemMessage", "");
    },

    storeItemMessage({ commit }, message) {
        commit("setItemMessage", message);
    },

    // Ensures AddItem message and EditItem message don't conflict
    switchItemType({ commit }, type) {
        commit("setItemType", type);
    },

    resetItemStatus({ commit }) {
        commit("setItemStatus", false);
    },

    // Enable single item/modal image update on bottom item click
    bottomItemClick({ commit }, bool) {
        commit("setClicked", bool);
    }
};

const mutations = {
    setItems: (state, items) => (state.items = items),
    addItemProp: (state, item) => (state.itemProp = item),
    setItemMessage: (state, message) => (state.itemMessage = message),
    setItemType: (state, type) => (state.itemType = type),
    setItemStatus: (state, status) => (state.itemStatus = status),
    imageSrc: (state, image) => (state.image = image),
    mainImageSrc: (state, image) => (state.mainImage = image),
    setClicked: (state, bool) => (state.clicked = bool)
};

const state = {
    items: [],
    itemProp: {},
    itemMessage: "",
    itemType: "",
    itemStatus: false,
    image: {},
    mainImage: {},
    clicked: false
};

const getters = {
    allItems: state => state.items,
    getItem: state => state.itemProp,
    getItemMessage: state => state.itemMessage,
    getItemType: state => state.itemType,
    getItemStatus: state => state.itemStatus,
    getSrc: state => state.image,
    getMainImage: state => state.mainImage,
    getClicked: state => state.clicked
};

export default {
    state,
    getters,
    actions,
    mutations
};
