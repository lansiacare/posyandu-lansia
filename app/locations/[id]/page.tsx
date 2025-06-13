"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { MapPin, ArrowLeft, Users, Calendar } from "lucide-react"
import Link from "next/link"
import { RegistrationForm } from "@/components/registration-form"

// Generate random dates for June 2025 (all Saturdays)
const generateJune2025Saturdays = () => {
  const saturdays = [7, 14, 21, 28] // All Saturdays in June 2025
  return saturdays.map((day) => {
    const date = `2025-06-${day.toString().padStart(2, "0")}`
    const registered = Math.floor(Math.random() * 15) + 5 // Random between 5-20 people
    return { date, available: true, registered }
  })
}

// Generate patient names
const generatePatientNames = (count: number) => {
  const firstNames = [
    "Siti",
    "Budi",
    "Ani",
    "Ahmad",
    "Ratna",
    "Joko",
    "Sri",
    "Hadi",
    "Dewi",
    "Agus",
    "Rina",
    "Bambang",
    "Wati",
    "Dedi",
    "Lina",
    "Eko",
    "Yuni",
    "Rudi",
    "Lia",
    "Tono",
    "Nita",
    "Hendra",
    "Maya",
    "Dodi",
    "Ika",
    "Surya",
    "Emi",
    "Andi",
    "Sari",
    "Dani",
  ]

  const lastNames = [
    "Wijaya",
    "Sari",
    "Santoso",
    "Dewi",
    "Susanto",
    "Wati",
    "Saputra",
    "Lestari",
    "Hidayat",
    "Utami",
    "Nugroho",
    "Rahayu",
    "Setiawan",
    "Astuti",
    "Kusuma",
    "Wulandari",
    "Putra",
    "Susilawati",
    "Pratama",
    "Purnama",
    "Permadi",
    "Handayani",
    "Gunawan",
    "Pertiwi",
    "Suryanto",
    "Anggraini",
    "Firmansyah",
    "Hartati",
    "Cahyono",
    "Safitri",
  ]

  const patients = []
  for (let i = 0; i < count; i++) {
    const firstName = firstNames[Math.floor(Math.random() * firstNames.length)]
    const lastName = lastNames[Math.floor(Math.random() * lastNames.length)]
    const age = Math.floor(Math.random() * 20) + 60 // Age between 60-80
    patients.push(`${firstName} ${lastName} (${age} tahun)`)
  }

  return patients
}

const locationData = {
  condongcatur: {
    name: "Posyandu Bunga Anyelir",
    address: "RW. 17 Blok. II Perumnas CC, Dusun Dero, Condongcatur, Depok, Sleman, DIY",
    description:
      "Posyandu Bunga Anyelir merupakan fasilitas kesehatan terpadu yang melayani masyarakat lansia dengan tenaga medis berpengalaman dan fasilitas modern. Kami berkomitmen memberikan pelayanan kesehatan terbaik untuk meningkatkan kualitas hidup lansia.",
    image: "/gambar/posyandu-condongcatur.png",
    schedule: generateJune2025Saturdays(),
    patients: {}, // Will be populated dynamically based on selected date
  },
}

export default function LocationDetailPage({ params }: { params: { id: string } }) {
  const [selectedDate, setSelectedDate] = useState<string | null>(null)
  const [showRegistration, setShowRegistration] = useState(false)
  const [registeredDates, setRegisteredDates] = useState<string[]>([])
  const [patientsList, setPatientsList] = useState<string[]>([])

  const location = locationData[params.id as keyof typeof locationData] || locationData.condongcatur

  const handleDateSelect = (date: string, registeredCount: number) => {
    setSelectedDate(date)

    // Generate patient names based on registered count
    if (!location.patients[date]) {
      location.patients[date] = generatePatientNames(registeredCount)
    }

    setPatientsList(location.patients[date] || [])
  }

  const handleRegistrationSuccess = (queueNumber: number) => {
    if (selectedDate) {
      setRegisteredDates((prev) => [...prev, selectedDate])
    }
    setShowRegistration(false)
    setSelectedDate(null)
    setPatientsList([])
    alert(`Pendaftaran berhasil! Nomor antrian Anda: ${queueNumber}`)
  }

  if (showRegistration && selectedDate) {
    return (
      <RegistrationForm
        selectedDate={selectedDate}
        locationName={location.name}
        onBack={() => setShowRegistration(false)}
        onSuccess={handleRegistrationSuccess}
      />
    )
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white">
      <div className="max-w-4xl mx-auto p-4">
        <div className="mb-6">
          <Link href="/locations">
            <Button variant="ghost" className="mb-4">
              <ArrowLeft className="mr-2 h-4 w-4" />
              Kembali ke Pilihan Lokasi
            </Button>
          </Link>
        </div>

        {/* Location Header */}
        <Card className="mb-6">
          <div className="aspect-video bg-gray-200 rounded-t-lg">
            <img
              src={location.image || "/placeholder.svg"}
              alt={location.name}
              className="w-full h-full object-cover rounded-t-lg"
            />
          </div>
          <CardHeader>
            <CardTitle className="text-2xl">{location.name}</CardTitle>
            <CardDescription className="flex items-center">
              <MapPin className="mr-1 h-4 w-4" />
              {location.address}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <p className="text-gray-600 mb-4">{location.description}</p>
          </CardContent>
        </Card>

        <div className="grid md:grid-cols-2 gap-6">
          {/* Schedule */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center">
                <Calendar className="mr-2 h-5 w-5" />
                Jadwal Posyandu
              </CardTitle>
              <CardDescription>Pilih tanggal untuk mendaftar</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {location.schedule.map((schedule) => {
                  const isRegistered = registeredDates.includes(schedule.date)
                  const isSelected = selectedDate === schedule.date

                  return (
                    <div
                      key={schedule.date}
                      className={`p-3 border rounded-lg cursor-pointer transition-colors ${
                        isSelected
                          ? "border-blue-500 bg-blue-50"
                          : isRegistered
                            ? "border-green-500 bg-green-50"
                            : "border-gray-200 hover:border-gray-300"
                      }`}
                      onClick={() => !isRegistered && handleDateSelect(schedule.date, schedule.registered)}
                    >
                      <div className="flex justify-between items-center">
                        <div>
                          <p className="font-medium">
                            {new Date(schedule.date).toLocaleDateString("id-ID", {
                              weekday: "long",
                              year: "numeric",
                              month: "long",
                              day: "numeric",
                            })}
                          </p>
                          <p className="text-sm text-gray-600">{schedule.registered} orang terdaftar</p>
                        </div>
                        {isRegistered && (
                          <Badge variant="secondary" className="bg-green-100 text-green-800">
                            Terdaftar
                          </Badge>
                        )}
                      </div>
                    </div>
                  )
                })}
              </div>

              {selectedDate && (
                <Button className="w-full mt-4" onClick={() => setShowRegistration(true)}>
                  Daftar untuk Tanggal Ini
                </Button>
              )}
            </CardContent>
          </Card>

          {/* Patient List */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center">
                <Users className="mr-2 h-5 w-5" />
                Pasien Terdaftar
              </CardTitle>
              <CardDescription>
                {selectedDate
                  ? `Daftar pasien terdaftar pada ${new Date(selectedDate).toLocaleDateString("id-ID", {
                      weekday: "long",
                      day: "numeric",
                      month: "long",
                      year: "numeric",
                    })}`
                  : "Pilih tanggal untuk melihat daftar pasien"}
              </CardDescription>
            </CardHeader>
            <CardContent>
              {selectedDate ? (
                <div className="space-y-2">
                  {patientsList.map((patient, index) => (
                    <div key={index} className="p-2 bg-gray-50 rounded">
                      <p className="text-sm">{patient}</p>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="flex flex-col items-center justify-center py-8 text-center text-gray-500">
                  <Users className="h-12 w-12 mb-2 opacity-30" />
                  <p>Pilih tanggal untuk melihat daftar pasien terdaftar</p>
                </div>
              )}
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  )
}
