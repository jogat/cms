
<script>
export default {
    name: "globals",
    data() {
        return {
            sideBarOpen: true,
        }
    },
    methods: {
        $toggleSideBar() {
            this.sideBarOpen = !this.sideBarOpen;
        },
        $closeSideBar() {
            this.sideBarOpen = false;
        }
    }
}
</script>
