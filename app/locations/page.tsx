"use client"

import { useState, useEffect } from "react"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { MapPin, ArrowLeft, Clock, Users } from "lucide-react"
import Link from "next/link"
import { useRouter } from "next/navigation"

const locations = [
  {
    id: 1,
    name: "Posyandu Condongcatur",
    address: "Jl. Kaliurang KM 7, Condongcatur, Depok, Sleman",
    description: "Posyandu dengan fasilitas lengkap dan tenaga medis berpengalaman",
    patients: 45,
    nextSchedule: "15 Januari 2024",
  },
  {
    id: 2,
    name: "Posyandu Caturtunggal",
    address: "Jl. Babarsari, Caturtunggal, Depok, Sleman",
    description: "Posyandu modern dengan layanan kesehatan terpadu untuk lansia",
    patients: 38,
    nextSchedule: "16 Januari 2024",
  },
  {
    id: 3,
    name: "Posyandu Maguwoharjo",
    address: "Jl. Raya Maguwoharjo, Maguwoharjo, Depok, Sleman",
    description: "Posyandu dengan akses mudah dan lingkungan yang nyaman",
    patients: 52,
    nextSchedule: "17 Januari 2024",
  },
]

export default function LocationsPage() {
  const [isLoggedIn, setIsLoggedIn] = useState(false)
  const router = useRouter()

  useEffect(() => {
    const userData = localStorage.getItem("user")
    if (!userData) {
      router.push("/login")
      return
    }
    setIsLoggedIn(true)
  }, [router])

  if (!isLoggedIn) {
    return (
      <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p>Memuat...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white">
      <div className="max-w-4xl mx-auto p-4">
        <div className="mb-6">
          <Link href="/">
            <Button variant="outline" className="mb-4">
              <ArrowLeft className="mr-2 h-4 w-4" />
              Kembali ke Beranda
            </Button>
          </Link>
          <h1 className="text-3xl font-bold text-gray-900 mb-2">Pilih Lokasi Posyandu</h1>
          <p className="text-gray-600">Pilih lokasi Posyandu yang paling dekat dengan Anda</p>
        </div>

        <div className="grid gap-6">
          {locations.map((location) => (
            <Card key={location.id} className="hover:shadow-lg transition-shadow">
              <CardHeader>
                <div className="flex items-start justify-between">
                  <div>
                    <CardTitle className="text-xl">{location.name}</CardTitle>
                    <CardDescription className="flex items-center mt-2">
                      <MapPin className="mr-1 h-4 w-4" />
                      {location.address}
                    </CardDescription>
                  </div>
                </div>
              </CardHeader>
              <CardContent>
                <p className="text-gray-600 mb-4">{location.description}</p>

                <div className="flex items-center space-x-6 mb-4 text-sm text-gray-600">
                  <div className="flex items-center">
                    <Users className="mr-1 h-4 w-4" />
                    {location.patients} pasien terdaftar
                  </div>
                  <div className="flex items-center">
                    <Clock className="mr-1 h-4 w-4" />
                    Jadwal berikutnya: {location.nextSchedule}
                  </div>
                </div>

                <Link href={`/locations/${location.id}`}>
                  <Button className="w-full">Lihat Detail & Daftar</Button>
                </Link>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </div>
  )
}
