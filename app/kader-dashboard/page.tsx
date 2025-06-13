"use client"

import type React from "react"

import { useState, useEffect } from "react"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { MapPin, Calendar, ChevronDown, FileEdit, LogOut, User, HelpCircle } from "lucide-react"
import Link from "next/link"
import { useRouter } from "next/navigation"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"

interface Patient {
  id: number
  queueNumber: number
  name: string
  gender: string
  age: number
  address: string
  hasExamination: boolean
}

interface Schedule {
  id: number
  date: string
  formattedDate: string
}

export default function KaderDashboardPage() {
  const [user, setUser] = useState<any>(null)
  const [selectedSchedule, setSelectedSchedule] = useState<string>("")
  const [schedules, setSchedules] = useState<Schedule[]>([])
  const [patients, setPatients] = useState<Patient[]>([])
  const router = useRouter()

  // Generate sample schedules for the next 7 days
  useEffect(() => {
    const generateSchedules = () => {
      const dates: Schedule[] = []
      const today = new Date()

      for (let i = 0; i < 7; i++) {
        const date = new Date(today)
        date.setDate(today.getDate() + i)

        const dateString = date.toISOString().split("T")[0]
        const options: Intl.DateTimeFormatOptions = {
          weekday: "long",
          year: "numeric",
          month: "long",
          day: "numeric",
        }
        const formattedDate = date.toLocaleDateString("id-ID", options)

        dates.push({
          id: i + 1,
          date: dateString,
          formattedDate: formattedDate,
        })
      }

      setSchedules(dates)
      // Set today as default selected date
      setSelectedSchedule(dates[0].date)
    }

    generateSchedules()
  }, [])

  // Generate sample patients based on selected date
  useEffect(() => {
    if (selectedSchedule) {
      // In a real app, this would be an API call to get patients for the selected date
      const samplePatients: Patient[] = [
        {
          id: 1,
          queueNumber: 1,
          name: "Siti Aminah",
          gender: "P",
          age: 67,
          address: "Jl. Mawar No. 123, Condongcatur",
          hasExamination: false,
        },
        {
          id: 2,
          queueNumber: 2,
          name: "Budi Santoso",
          gender: "L",
          age: 72,
          address: "Jl. Melati No. 456, Caturtunggal",
          hasExamination: true,
        },
        {
          id: 3,
          queueNumber: 3,
          name: "Mariam Sari",
          gender: "P",
          age: 69,
          address: "Jl. Anggrek No. 789, Maguwoharjo",
          hasExamination: false,
        },
        {
          id: 4,
          queueNumber: 4,
          name: "Ahmad Wijaya",
          gender: "L",
          age: 75,
          address: "Jl. Kaliurang KM 8, Condongcatur",
          hasExamination: false,
        },
        {
          id: 5,
          queueNumber: 5,
          name: "Ratna Dewi",
          gender: "P",
          age: 68,
          address: "Jl. Babarsari No. 101, Caturtunggal",
          hasExamination: false,
        },
      ]

      setPatients(samplePatients)
    }
  }, [selectedSchedule])

  useEffect(() => {
    const userData = localStorage.getItem("user")
    if (!userData) {
      router.push("/login")
      return
    }

    const parsedUser = JSON.parse(userData)
    if (parsedUser.role !== "kader") {
      router.push("/")
      return
    }

    setUser(parsedUser)
  }, [router])

  const handleLogout = () => {
    localStorage.removeItem("user")
    router.push("/")
  }

  const handleScheduleChange = (date: string) => {
    setSelectedSchedule(date)
  }

  if (!user) {
    return (
      <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto mb-4"></div>
          <p>Memuat...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white">
      {/* Header */}
      <header className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center space-x-4">
              <div className="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                <span className="text-white font-bold text-lg">K</span>
              </div>
              <div>
                <h1 className="text-xl font-bold text-gray-900">Dashboard Kader</h1>
                <p className="text-sm text-gray-600">Posyandu Condongcatur</p>
              </div>
            </div>

            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="ghost" className="flex items-center space-x-2">
                  <Avatar className="h-8 w-8">
                    <AvatarImage src="/placeholder.svg?height=32&width=32" alt="User" />
                    <AvatarFallback className="bg-green-100 text-green-600">{user.name.charAt(0)}</AvatarFallback>
                  </Avatar>
                  <span className="hidden md:inline-block">{user.name}</span>
                  <ChevronDown className="h-4 w-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                <DropdownMenuItem>
                  <User className="mr-2 h-4 w-4" />
                  <span>Profil Kader</span>
                </DropdownMenuItem>
                <DropdownMenuItem>
                  <HelpCircle className="mr-2 h-4 w-4" />
                  <span>Bantuan</span>
                </DropdownMenuItem>
                <DropdownMenuItem onClick={handleLogout}>
                  <LogOut className="mr-2 h-4 w-4" />
                  <span>Keluar</span>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </div>
      </header>

      <div className="max-w-7xl mx-auto p-4">
        {/* Location Info */}
        <Card className="mb-6">
          <CardHeader>
            <CardTitle className="text-2xl">Posyandu Condongcatur</CardTitle>
            <div className="flex items-center text-gray-600 mt-2">
              <MapPin className="mr-2 h-4 w-4" />
              Jl. Kaliurang KM 7, Condongcatur, Depok, Sleman, DIY 55283
            </div>
          </CardHeader>
        </Card>

        {/* Schedule Selector and Patient Table */}
        <Card>
          <CardHeader className="pb-3">
            <CardTitle className="flex items-center">
              <Calendar className="mr-2 h-5 w-5 text-green-600" />
              Jadwal Posyandu
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="mb-6">
              <Label htmlFor="schedule">Pilih Tanggal</Label>
              <Select value={selectedSchedule} onValueChange={handleScheduleChange}>
                <SelectTrigger className="w-full md:w-[300px]">
                  <SelectValue placeholder="Pilih tanggal jadwal" />
                </SelectTrigger>
                <SelectContent>
                  {schedules.map((schedule) => (
                    <SelectItem key={schedule.id} value={schedule.date}>
                      {schedule.formattedDate}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="rounded-md border">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead className="w-[80px]">No. Antrian</TableHead>
                    <TableHead>Nama Pasien</TableHead>
                    <TableHead className="w-[100px]">Jenis Kelamin</TableHead>
                    <TableHead className="w-[80px]">Umur</TableHead>
                    <TableHead>Alamat</TableHead>
                    <TableHead className="w-[100px]">Status</TableHead>
                    <TableHead className="w-[100px] text-right">Aksi</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {patients.map((patient) => (
                    <TableRow key={patient.id}>
                      <TableCell className="font-medium">{patient.queueNumber}</TableCell>
                      <TableCell>{patient.name}</TableCell>
                      <TableCell>{patient.gender === "L" ? "Laki-laki" : "Perempuan"}</TableCell>
                      <TableCell>{patient.age} tahun</TableCell>
                      <TableCell className="max-w-[200px] truncate">{patient.address}</TableCell>
                      <TableCell>
                        <Badge variant={patient.hasExamination ? "default" : "secondary"}>
                          {patient.hasExamination ? "Sudah Diperiksa" : "Belum Diperiksa"}
                        </Badge>
                      </TableCell>
                      <TableCell className="text-right">
                        <Link href={`/input-pemeriksaan/${patient.id}`}>
                          <Button size="sm" variant="outline">
                            <FileEdit className="h-4 w-4 mr-1" />
                            {patient.hasExamination ? "Edit" : "Input"}
                          </Button>
                        </Link>
                      </TableCell>
                    </TableRow>
                  ))}
                  {patients.length === 0 && (
                    <TableRow>
                      <TableCell colSpan={7} className="h-24 text-center">
                        Tidak ada pasien terdaftar untuk tanggal ini
                      </TableCell>
                    </TableRow>
                  )}
                </TableBody>
              </Table>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}

function Label({ htmlFor, children }: { htmlFor?: string; children: React.ReactNode }) {
  return (
    <label htmlFor={htmlFor} className="block text-sm font-medium text-gray-700 mb-1">
      {children}
    </label>
  )
}
