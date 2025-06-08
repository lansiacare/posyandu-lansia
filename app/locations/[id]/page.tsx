"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { MapPin, ArrowLeft, Users, Calendar } from "lucide-react"
import Link from "next/link"
import { RegistrationForm } from "@/components/registration-form"

const locationData = {
  condongcatur: {
    name: "Posyandu Condongcatur",
    address: "Jl. Kaliurang KM 7, Condongcatur, Depok, Sleman, DIY 55283",
    description:
      "Posyandu Condongcatur merupakan fasilitas kesehatan terpadu yang melayani masyarakat lansia dengan tenaga medis berpengalaman dan fasilitas modern. Kami berkomitmen memberikan pelayanan kesehatan terbaik untuk meningkatkan kualitas hidup lansia.",
    image: "/placeholder.svg?height=300&width=600",
    schedule: [
      { date: "2024-01-15", available: true, registered: 12 },
      { date: "2024-01-22", available: true, registered: 8 },
      { date: "2024-01-29", available: true, registered: 15 },
      { date: "2024-02-05", available: true, registered: 5 },
    ],
    patients: [
      "Siti Aminah (67 tahun)",
      "Budi Santoso (72 tahun)",
      "Mariam Sari (69 tahun)",
      "Ahmad Wijaya (75 tahun)",
      "Ratna Dewi (68 tahun)",
    ],
  },
}

export default function LocationDetailPage({ params }: { params: { id: string } }) {
  const [selectedDate, setSelectedDate] = useState<string | null>(null)
  const [showRegistration, setShowRegistration] = useState(false)
  const [registeredDates, setRegisteredDates] = useState<string[]>([])

  const location = locationData[params.id as keyof typeof locationData] || locationData.condongcatur

  const handleDateSelect = (date: string) => {
    setSelectedDate(date)
  }

  const handleRegistrationSuccess = (queueNumber: number) => {
    if (selectedDate) {
      setRegisteredDates((prev) => [...prev, selectedDate])
    }
    setShowRegistration(false)
    setSelectedDate(null)
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
                      onClick={() => !isRegistered && handleDateSelect(schedule.date)}
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
                Daftar Pasien Terdaftar
              </CardTitle>
              <CardDescription>Pasien yang sudah terdaftar bulan ini</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-2">
                {location.patients.map((patient, index) => (
                  <div key={index} className="p-2 bg-gray-50 rounded">
                    <p className="text-sm">{patient}</p>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  )
}
