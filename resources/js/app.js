import './bootstrap'
// Import Vue
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import App from './App.vue'
import router from './router'

// PrimeVue
import PrimeVue from 'primevue/config'
// Theme (PrimeVue 4 uses themes differently)
import Aura from '@primeuix/themes/aura'
import {definePreset} from '@primeuix/themes'

// Icons
import 'primeicons/primeicons.css'

// Sakai styles
import './assets/tailwind.css'
import './assets/styles.scss'

// Import PrimeVue Components
// Form Components
import AutoComplete from 'primevue/autocomplete'
import CascadeSelect from 'primevue/cascadeselect'
import Checkbox from 'primevue/checkbox'
import CheckboxGroup from 'primevue/checkboxgroup'
import ColorPicker from 'primevue/colorpicker'
import DatePicker from 'primevue/datepicker'
import Editor from 'primevue/editor'
import FloatLabel from 'primevue/floatlabel'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import IftaLabel from 'primevue/iftalabel'
import InputGroup from 'primevue/inputgroup'
import InputGroupAddon from 'primevue/inputgroupaddon'
import InputMask from 'primevue/inputmask'
import InputNumber from 'primevue/inputnumber'
import InputOtp from 'primevue/inputotp'
import InputText from 'primevue/inputtext'
import KeyFilter from 'primevue/keyfilter'
import Knob from 'primevue/knob'
import Listbox from 'primevue/listbox'
import MultiSelect from 'primevue/multiselect'
import Password from 'primevue/password'
import RadioButton from 'primevue/radiobutton'
import RadioButtonGroup from 'primevue/radiobuttongroup'
import Rating from 'primevue/rating'
import Select from 'primevue/select'
import SelectButton from 'primevue/selectbutton'
import Slider from 'primevue/slider'
import Textarea from 'primevue/textarea'
import ToggleButton from 'primevue/togglebutton'
import ToggleSwitch from 'primevue/toggleswitch'
import TreeSelect from 'primevue/treeselect'
// Button Components
import Button from 'primevue/button'
import SpeedDial from 'primevue/speeddial'
import SplitButton from 'primevue/splitbutton'
// Data Components
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import ColumnGroup from 'primevue/columngroup'
import Row from 'primevue/row'
import DataView from 'primevue/dataview'
import OrderList from 'primevue/orderlist'
import OrganizationChart from 'primevue/organizationchart'
import Paginator from 'primevue/paginator'
import PickList from 'primevue/picklist'
import Timeline from 'primevue/timeline'
import Tree from 'primevue/tree'
import TreeTable from 'primevue/treetable'
import VirtualScroller from 'primevue/virtualscroller'
// Panel Components
import Accordion from 'primevue/accordion'
import AccordionPanel from 'primevue/accordionpanel'
import AccordionHeader from 'primevue/accordionheader'
import AccordionContent from 'primevue/accordioncontent'
import Card from 'primevue/card'
import DeferredContent from 'primevue/deferredcontent'
import Divider from 'primevue/divider'
import Fieldset from 'primevue/fieldset'
import Panel from 'primevue/panel'
import ScrollPanel from 'primevue/scrollpanel'
import Splitter from 'primevue/splitter'
import SplitterPanel from 'primevue/splitterpanel'
import Stepper from 'primevue/stepper'
import StepList from 'primevue/steplist'
import StepPanels from 'primevue/steppanels'
import StepItem from 'primevue/stepitem'
import Step from 'primevue/step'
import StepPanel from 'primevue/steppanel'
import Tabs from 'primevue/tabs'
import TabList from 'primevue/tablist'
import Tab from 'primevue/tab'
import TabPanels from 'primevue/tabpanels'
import TabPanel from 'primevue/tabpanel'
import Toolbar from 'primevue/toolbar'
// Overlay Components
import ConfirmDialog from 'primevue/confirmdialog'
import ConfirmationService from 'primevue/confirmationservice'
import ConfirmPopup from 'primevue/confirmpopup'
import Dialog from 'primevue/dialog'
import Drawer from 'primevue/drawer'
import DynamicDialog from 'primevue/dynamicdialog'
import DialogService from 'primevue/dialogservice'
import Popover from 'primevue/popover'
import Tooltip from 'primevue/tooltip'
// File Components
import FileUpload from 'primevue/fileupload'
// Menu Components
import Breadcrumb from 'primevue/breadcrumb'
import ContextMenu from 'primevue/contextmenu'
import Dock from 'primevue/dock'
import Menu from 'primevue/menu'
import Menubar from 'primevue/menubar'
import MegaMenu from 'primevue/megamenu'
import PanelMenu from 'primevue/panelmenu'
import TieredMenu from 'primevue/tieredmenu'
// Chart Components
import Chart from 'primevue/chart'
// Messages Components
import Message from 'primevue/message'
import Toast from 'primevue/toast'
import ToastService from 'primevue/toastservice'
// Media Components
import Carousel from 'primevue/carousel'
import Galleria from 'primevue/galleria'
import Image from 'primevue/image'
import ImageCompare from 'primevue/imagecompare'
// Misc Components
import AnimateOnScroll from 'primevue/animateonscroll'
import Avatar from 'primevue/avatar'
import AvatarGroup from 'primevue/avatargroup' //Optional for grouping
import Badge from 'primevue/badge'
import OverlayBadge from 'primevue/overlaybadge'
import BlockUI from 'primevue/blockui'
import Chip from 'primevue/chip'
import FocusTrap from 'primevue/focustrap'
import Fluid from 'primevue/fluid'
import Inplace from 'primevue/inplace'
import MeterGroup from 'primevue/metergroup'
import ProgressBar from 'primevue/progressbar'
import ProgressSpinner from 'primevue/progressspinner'
import ScrollTop from 'primevue/scrolltop'
import Skeleton from 'primevue/skeleton'
import Ripple from 'primevue/ripple'
import StyleClass from 'primevue/styleclass'
import Tag from 'primevue/tag'

const MyPreset = definePreset(Aura, {
	semantic: {
		colorScheme: {
			light: {
				//...
			},
			dark: {
				//...
			},
		},
	},
})

const pinia = createPinia()

const app = createApp(App)
app.use(PrimeVue, {
	theme: {
		preset: MyPreset,
		options: {
			darkModeSelector: '.app-dark',
		},
	},
})

// Use directives globally
app.directive('keyfilter', KeyFilter)
app.directive('tooltip', Tooltip)
app.directive('animateonscroll', AnimateOnScroll)
app.directive('focustrap', FocusTrap)
app.directive('ripple', Ripple)
app.directive('styleclass', StyleClass)

// Use services globally
app.use(ConfirmationService)
app.use(DialogService)
app.use(ToastService)

// Register components globally
app.component('AutoComplete', AutoComplete)
app.component('CascadeSelect', CascadeSelect)
app.component('Checkbox', Checkbox)
app.component('CheckboxGroup', CheckboxGroup)
app.component('ColorPicker', ColorPicker)
app.component('DatePicker', DatePicker)
app.component('Editor', Editor)
app.component('FloatLabel', FloatLabel)
app.component('IconField', IconField)
app.component('InputIcon', InputIcon)
app.component('IftaLabel', IftaLabel)
app.component('InputGroup', InputGroup)
app.component('InputGroupAddon', InputGroupAddon)
app.component('InputMask', InputMask)
app.component('InputNumber', InputNumber)
app.component('InputOtp', InputOtp)
app.component('InputText', InputText)
app.component('KeyFilter', KeyFilter)
app.component('Knob', Knob)
app.component('Listbox', Listbox)
app.component('MultiSelect', MultiSelect)
app.component('Password', Password)
app.component('RadioButton', RadioButton)
app.component('RadioButtonGroup', RadioButtonGroup)
app.component('Rating', Rating)
app.component('Select', Select)
app.component('SelectButton', SelectButton)
app.component('Slider', Slider)
app.component('Textarea', Textarea)
app.component('ToggleButton', ToggleButton)
app.component('ToggleSwitch', ToggleSwitch)
app.component('TreeSelect', TreeSelect)
app.component('Button', Button)
app.component('SpeedDial', SpeedDial)
app.component('SplitButton', SplitButton)
app.component('DataTable', DataTable)
app.component('Column', Column)
app.component('ColumnGroup', ColumnGroup)
app.component('Row', Row)
app.component('DataView', DataView)
app.component('OrderList', OrderList)
app.component('OrganizationChart', OrganizationChart)
app.component('Paginator', Paginator)
app.component('PickList', PickList)
app.component('Timeline', Timeline)
app.component('Tree', Tree)
app.component('TreeTable', TreeTable)
app.component('VirtualScroller', VirtualScroller)
app.component('Accordion', Accordion)
app.component('AccordionPanel', AccordionPanel)
app.component('AccordionHeader', AccordionHeader)
app.component('AccordionContent', AccordionContent)
app.component('Card', Card)
app.component('DeferredContent', DeferredContent)
app.component('Divider', Divider)
app.component('Fieldset', Fieldset)
app.component('Panel', Panel)
app.component('ScrollPanel', ScrollPanel)
app.component('Splitter', Splitter)
app.component('SplitterPanel', SplitterPanel)
app.component('Stepper', Stepper)
app.component('StepList', StepList)
app.component('StepPanels', StepPanels)
app.component('StepItem', StepItem)
app.component('Step', Step)
app.component('StepPanel', StepPanel)
app.component('Tabs', Tabs)
app.component('TabList', TabList)
app.component('Tab', Tab)
app.component('TabPanels', TabPanels)
app.component('TabPanel', TabPanel)
app.component('Toolbar', Toolbar)
app.component('ConfirmDialog', ConfirmDialog)
app.component('ConfirmPopup', ConfirmPopup)
app.component('Dialog', Dialog)
app.component('Drawer', Drawer)
app.component('DynamicDialog', DynamicDialog)
app.component('Popover', Popover)
app.component('FileUpload', FileUpload)
app.component('Breadcrumb', Breadcrumb)
app.component('ContextMenu', ContextMenu)
app.component('Dock', Dock)
app.component('Menu', Menu)
app.component('Menubar', Menubar)
app.component('MegaMenu', MegaMenu)
app.component('PanelMenu', PanelMenu)
app.component('TieredMenu', TieredMenu)
app.component('Chart', Chart)
app.component('Message', Message)
app.component('Toast', Toast)
app.component('Carousel', Carousel)
app.component('Galleria', Galleria)
app.component('Image', Image)
app.component('ImageCompare', ImageCompare)
app.component('Avatar', Avatar)
app.component('AvatarGroup', AvatarGroup) //Optional for grouping
app.component('Badge', Badge)
app.component('OverlayBadge', OverlayBadge)
app.component('BlockUI', BlockUI)
app.component('Chip', Chip)
app.component('Fluid', Fluid)
app.component('Inplace', Inplace)
app.component('MeterGroup', MeterGroup)
app.component('ProgressBar', ProgressBar)
app.component('ProgressSpinner', ProgressSpinner)
app.component('ScrollTop', ScrollTop)
app.component('Skeleton', Skeleton)
app.component('Tag', Tag)

app.use(pinia).use(router).mount('#app')
