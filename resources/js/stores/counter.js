// stores/counter.js
import {defineStore} from 'pinia'

const useCounterStore = defineStore('counter', {
	state: () => {
		return {count: 0}
	},
	// could also be defined as
	// state: () => ({ count: 0 })
	actions: {
		increment() {
			this.count++
		},
	},
})

export default useCounterStore
