<template>
    <Sidebar collapsible="icon" variant="inset">
      <SidebarHeader>
        <SidebarMenu>
          <SidebarMenuItem>
            <SidebarMenuButton size="lg" as-child>
              <Link :href="route('myFiles')">
                <AppLogo />
              </Link>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarHeader>

      <SidebarContent>
        <NavMain :items="mainNavItems" />
      </SidebarContent>

      <SidebarFooter>
        <Dialog :open="form.hasErrors || openDialog" @update:open="val => openDialog = val">
          <DialogTrigger as-child>
            <SidebarMenuButton :as-child="true">
              <Button variant="outline" type="button">
                <Plus class="size-4" />
                <span>Create new</span>
              </Button>
            </SidebarMenuButton>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Create New Folder</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="createFolder">
              <label for="name" class="block text-sm font-medium mb-1 sr-only">Folder Name</label>
              <Input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Folder name"
                class="mb-2"
                :class="{ 'border-destructive': form.errors.name }"
              />
              <InputError :message="form.errors.name" class="mt-2" />

              <DialogFooter>
                <DialogClose as-child>
                  <Button type="button" variant="outline">Cancel</Button>
                </DialogClose>
                <Button type="submit" :disabled="form.processing || !form.name.trim()">Create</Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
        <NavUser />
      </SidebarFooter>
    </Sidebar>
    <slot />
  </template>

  <script setup lang="ts">
  import NavMain from '@/components/NavMain.vue'
  import NavUser from '@/components/NavUser.vue'
  import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'
  import { type NavItem } from '@/types'
  import { Link, useForm } from '@inertiajs/vue3'
  import { ExternalLink, Files, LayoutGrid, Trash, Plus } from 'lucide-vue-next'
  import AppLogo from './AppLogo.vue'
  import { Dialog, DialogTrigger, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from '@/components/ui/dialog'
  import { Input } from '@/components/ui/input'
  import { Button } from '@/components/ui/button'
  import InputError from '@/components/InputError.vue'
  import { ref } from 'vue'

  const openDialog = ref(false)

  const mainNavItems: NavItem[] = [
    { title: 'My Files', href: '/my-files', icon: Files },
    { title: 'Shared with me', href: '#', icon: ExternalLink },
    { title: 'Shared by me', href: '#', icon: LayoutGrid },
    { title: 'Trash', href: '#', icon: Trash },
  ]

  const form = useForm({ name: '' })

  function createFolder() {
    form.post('/folders', {
      preserveScroll: true,
      onSuccess: () => {
        form.reset('name')
        openDialog.value = false
      },
      onError: () => {
        openDialog.value = true
      }
    })
  }
  </script>
