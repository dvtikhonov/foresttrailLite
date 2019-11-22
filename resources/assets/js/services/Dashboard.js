
export default class Cashier {

    constructor() {
        this.app = null;
        this.auth = {
            name: null,
            roleId: null,
            token: null,
        };
        this.notSavedOrders = [];
        this.bufferOrders = [];
        this.currentOrder = null;
        this.selectedProduct = {
            orderItemId: null,
            id: null,
            quantity: null,
        };
        this.menu = null;

        // Сохранение заказов из буфера, через 5 мин бездействия
        this.idlePeriod = 60000 * 5;
        this.intervalSaving = null;
        let _that = this;
        this.resetIdleTime();
        window.onclick = function() {
            _that.resetIdleTime();
        }

    }

    resetIdleTime(){
        clearInterval(this.intervalSaving);
        let _that = this;
        this.intervalSaving = setInterval(function () {
            _that.saveOrders(_that.bufferOrders);
        }, this.idlePeriod);
    }

    /**
     * Получение элемента меню по ID (возможен поиск по частному меню, второй параметр)
     * @param id
     * @param menu
     * @returns {*}
     */
    menuProduct(id, menu = this.menu){
        for (let category in menu) {
            let product = null;
            let findProduct = menu[category].some(function(currentProduct, i, arr) {
                if(currentProduct.id === id){
                    product = currentProduct;
                    return true;
                }
            });
            if(findProduct) {return product;}
        }
    }

    saveOrders(orders = this.bufferOrders){
        return new Promise((resolve, reject) => {
            // начало цепочки
            let chain = Promise.resolve();
            let results = [];
            let _that = this;

            orders.forEach(function(order) {
                chain = chain
                    .then(() => _that.saveOrder(order))
                    .then((result) => {
                        results.push(result);
                    });
            });

            chain.then(() => {
                resolve();
                this.bufferOrders = [];
            });
        });
    }

    saveOrder(order){
        return new Promise((resolve, reject) => {
            let items = [];
            order.items.forEach(function (foodItem) {
                items.push( foodItem.id + '|' + foodItem.quantity );
            });
            axios.post('/api/orders', {item : items}).then((response) => {
                resolve();
            }).catch(() => {
                reject();
            });
        });
    }
}