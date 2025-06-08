"use client"

import type React from "react"
import { useState, useEffect } from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { MapPin, UserIcon, LogOut, HelpCircle } from "lucide-react"
import Link from "next/link"

const healthStats = [
  {
    title: "Diabetes",
    percentage: 35,
    description: "Lansia dengan diabetes di wilayah Depok",
  },
  {
    title: "Hipertensi",
    percentage: 42,
    description: "Lansia dengan tekanan darah tinggi",
  },
  {
    title: "Stroke",
    percentage: 18,
    description: "Kasus stroke pada lansia",
  },
  {
    title: "Demensia",
    percentage: 12,
    description: "Lansia dengan gangguan kognitif",
  },
]

const healthArticles = [
  {
    title: "Tips Menjaga Kesehatan Jantung di Usia Lanjut",
    source: "Halodoc",
    url: "https://halodoc.com",
    image: "/placeholder.svg?height=200&width=300",
  },
  {
    title: "Pentingnya Olahraga Ringan untuk Lansia",
    source: "Kompas Health",
    url: "https://kompas.com",
    image: "/placeholder.svg?height=200&width=300",
  },
  {
    title: "Nutrisi Seimbang untuk Mencegah Diabetes",
    source: "Detik Health",
    url: "https://detik.com",
    image: "/placeholder.svg?height=200&width=300",
  },
]

interface AppUser {
  email: string
  name: string
  role?: string
}

export default function HomePage(): React.JSX.Element {
  const [currentStatIndex, setCurrentStatIndex] = useState<number>(0)
  const [user, setUser] = useState<AppUser | null>(null)
  const [isLoggedIn, setIsLoggedIn] = useState<boolean>(false)

  useEffect(() => {
    // Check if user is logged in
    const userData = localStorage.getItem("user")
    if (userData) {
      const parsedUser = JSON.parse(userData) as AppUser
      setUser(parsedUser)
      setIsLoggedIn(true)

      // If user is kader, redirect to kader dashboard
      if (parsedUser.role === "kader") {
        window.location.href = "/kader-dashboard"
      }
    }
  }, [])

  useEffect(() => {
    const interval = setInterval(() => {
      setCurrentStatIndex((prev) => (prev + 1) % healthStats.length)
    }, 5000)
    return () => clearInterval(interval)
  }, [])

  const handleLogout = (): void => {
    localStorage.removeItem("user")
    setUser(null)
    setIsLoggedIn(false)
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white">
      {/* Header */}
      <header className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center space-x-4">
              <div className="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                <span className="text-white font-bold text-lg">LC</span>
              </div>
              <div>
                <h1 className="text-xl font-bold text-gray-900">Lansia Care</h1>
                <p className="text-sm text-gray-600">Kecamatan Depok, Sleman, DIY</p>
              </div>
            </div>

            <div className="flex items-center space-x-4">
              {isLoggedIn && user ? (
                <DropdownMenu>
                  <DropdownMenuTrigger asChild>
                    <Button variant="ghost" className="relative h-8 w-8 rounded-full">
                      <Avatar className="h-8 w-8">
                        <AvatarImage src="/placeholder.svg?height=32&width=32" alt="User" />
                        <AvatarFallback>{user.name.charAt(0)}</AvatarFallback>
                      </Avatar>
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent className="w-56" align="end" forceMount>
                    <DropdownMenuItem asChild>
                      <Link href="/account" className="flex items-center">
                        <UserIcon className="mr-2 h-4 w-4" />
                        <span>Akun Saya</span>
                      </Link>
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
              ) : (
                <div className="flex items-center space-x-4">
                  <Link href="/login">
                    <Button variant="ghost">Masuk</Button>
                  </Link>
                  <Link href="/register">
                    <Button>Daftar</Button>
                  </Link>
                </div>
              )}
            </div>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section className="py-12 px-4 sm:px-6 lg:px-8">
        <div className="max-w-7xl mx-auto text-center">
          <h2 className="text-4xl font-bold text-gray-900 mb-4">Selamat Datang di Lansia Care</h2>
          <p className="text-xl text-gray-600 mb-8">
            Platform terpadu untuk layanan kesehatan lansia di Kecamatan Depok, Sleman
          </p>
          <div className="bg-white rounded-lg shadow-lg p-6 max-w-md mx-auto">
            {isLoggedIn && user ? (
              <>
                <h3 className="text-lg font-semibold mb-2">Halo, {user.name}!</h3>
                <p className="text-gray-600 mb-4">Pilih lokasi Posyandu untuk memulai</p>
                <Link href="/locations">
                  <Button className="w-full" size="lg">
                    <MapPin className="mr-2 h-5 w-5" />
                    Pilih Lokasi Posyandu
                  </Button>
                </Link>
              </>
            ) : (
              <>
                <h3 className="text-lg font-semibold mb-2">Mulai Gunakan Layanan Lansia Care</h3>
                <p className="text-gray-600 mb-4">Silakan masuk atau daftar untuk mengakses layanan</p>
                <div className="grid grid-cols-2 gap-4">
                  <Link href="/login">
                    <Button variant="outline" className="w-full">
                      Masuk
                    </Button>
                  </Link>
                  <Link href="/register">
                    <Button className="w-full">Daftar</Button>
                  </Link>
                </div>
              </>
            )}
          </div>
        </div>
      </section>

      {/* Health Statistics */}
      <section className="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div className="max-w-7xl mx-auto">
          <h3 className="text-2xl font-bold text-center mb-8">Statistik Kesehatan Lansia</h3>
          <div className="max-w-md mx-auto">
            <Card className="transition-all duration-500">
              <CardHeader className="text-center">
                <CardTitle className="text-3xl font-bold text-blue-600">
                  {healthStats[currentStatIndex].percentage}%
                </CardTitle>
                <CardDescription className="text-lg">{healthStats[currentStatIndex].title}</CardDescription>
              </CardHeader>
              <CardContent>
                <p className="text-center text-gray-600">{healthStats[currentStatIndex].description}</p>
                <div className="flex justify-center mt-4 space-x-2">
                  {healthStats.map((_, index) => (
                    <div
                      key={index}
                      className={`w-2 h-2 rounded-full ${index === currentStatIndex ? "bg-blue-600" : "bg-gray-300"}`}
                    />
                  ))}
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </section>

      {/* Health Articles */}
      <section className="py-12 px-4 sm:px-6 lg:px-8">
        <div className="max-w-7xl mx-auto">
          <h3 className="text-2xl font-bold text-center mb-8">Artikel Kesehatan</h3>
          <div className="grid md:grid-cols-3 gap-6">
            {healthArticles.map((article, index) => (
              <Card key={index} className="hover:shadow-lg transition-shadow cursor-pointer">
                <a href={article.url} target="_blank" rel="noopener noreferrer">
                  <div className="aspect-video bg-gray-200 rounded-t-lg">
                    <img
                      src={article.image || "/placeholder.svg"}
                      alt={article.title}
                      className="w-full h-full object-cover rounded-t-lg"
                    />
                  </div>
                  <CardHeader>
                    <CardTitle className="text-lg">{article.title}</CardTitle>
                    <CardDescription>Sumber: {article.source}</CardDescription>
                  </CardHeader>
                </a>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-gray-800 text-white py-8">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <div className="flex items-center justify-center mb-4">
            <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
              <span className="text-white font-bold">LC</span>
            </div>
            <span className="text-lg font-semibold">Lansia Care</span>
          </div>
          <p className="text-gray-300 mb-2">Platform terpadu layanan kesehatan lansia</p>
          <p className="text-gray-400">&copy; 2024 Lansia Care - Kecamatan Depok, Sleman, DIY</p>
        </div>
      </footer>
    </div>
  )
}
