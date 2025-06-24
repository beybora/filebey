<template>
    <Head title="My Files" />

    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div v-if="files.length" class="rounded-md border">
          <Table>
            <TableHeader>
              <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                <TableHead v-for="header in headerGroup.headers" :key="header.id">
                  <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                </TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow
                v-for="row in table.getRowModel().rows"
                :key="row.id"
                class="cursor-pointer hover:bg-muted/50 transition"
                @click="() => console.log('Clicked row', row.original)"
              >
                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                  <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <div
          v-else
          class="relative h-[200px] flex items-center justify-center rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
          <p class="text-muted-foreground text-sm">No files or folders yet.</p>
        </div>
      </div>

      <!-- Delete Modal -->
      <AlertDialog v-model:open="deleteDialog.open">
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Are you sure?</AlertDialogTitle>
            <AlertDialogDescription>
              This will permanently delete the folder. This action cannot be undone.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel @click="closeDialog">Cancel</AlertDialogCancel>
            <AlertDialogAction @click="deleteFolder">Delete</AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </AppLayout>
  </template>

  <script setup lang="ts">
  import AppLayout from '@/layouts/AppLayout.vue'
  import { Head, usePage, router } from '@inertiajs/vue3'
  import type { BreadcrumbItem } from '@/types'
  import { ref, h } from 'vue'
  import { Folder, File as FileIcon, MoreVertical } from 'lucide-vue-next'
  import { Button } from '@/components/ui/button'
  import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
  import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu'
  import { FlexRender, getCoreRowModel, useVueTable, type ColumnDef } from '@tanstack/vue-table'
  import { formatDate } from '../utils/format'
  import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle
  } from '@/components/ui/alert-dialog'

  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'My Files', href: '/my-files' },
  ]

  const page = usePage()
  const files = page.props.items?.data ?? []

  interface FileEntry {
    id: number
    name: string
    is_folder: number
    created_at: string | null
    updated_at: string | null
  }

  const deleteDialog = ref<{
    open: boolean
    fileId: number | null
  }>({
    open: false,
    fileId: null,
  })

  function openDeleteModal(id: number) {
    deleteDialog.value.open = true
    deleteDialog.value.fileId = id
  }

  function closeDialog() {
    deleteDialog.value.open = false
    deleteDialog.value.fileId = null
  }

  function deleteFolder() {
    if (!deleteDialog.value.fileId) return

    router.delete('/folders', {
      data: { id: deleteDialog.value.fileId },
      preserveScroll: true,
      onFinish: closeDialog,
    })
  }

  const columns: ColumnDef<FileEntry>[] = [
    {
      accessorKey: 'name',
      header: 'Name',
      cell: ({ row }) =>
        h('div', { class: 'flex items-center gap-2' }, [
          h(row.original.is_folder ? Folder : FileIcon, { class: 'h-4 w-4 text-muted-foreground' }),
          h('span', row.getValue('name')),
        ]),
    },
    {
      accessorKey: 'created_at',
      header: 'Created',
      cell: ({ row }) => h('span', formatDate(row.original.created_at)),
    },
    {
      accessorKey: 'updated_at',
      header: 'Updated',
      cell: ({ row }) => h('span', formatDate(row.original.updated_at)),
    },
    {
      id: 'actions',
      header: '',
      enableSorting: false,
      cell: ({ row }) => {
        const file = row.original
        return h('div', { class: 'text-right' }, [
          h(DropdownMenu, {}, {
            default: () => [
              h(DropdownMenuTrigger, {}, {
                default: () =>
                  h(Button, { variant: 'ghost', class: 'h-8 w-8 p-0' }, () =>
                    h(MoreVertical, { class: 'h-4 w-4' })
                  ),
              }),
              h(DropdownMenuContent, { align: 'end' }, {
                default: () => [
                  h(DropdownMenuItem, { onClick: () => console.log('View', file.id) }, () => 'View'),
                  h(DropdownMenuItem, { onClick: () => openDeleteModal(file.id) }, () => 'Delete'),
                  h(DropdownMenuItem, {}, () => 'Rename'),
                ],
              }),
            ],
          }),
        ])
      },
    },
  ]

  const table = useVueTable<FileEntry>({
    data: files,
    columns,
    getCoreRowModel: getCoreRowModel(),
    state: {},
  })
  </script>
