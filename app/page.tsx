"use client"

import type React from "react"
import { useState, useEffect } from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { MapPin, UserIcon, LogOut, HelpCircle, Phone, Mail, MapPinned, Globe } from "lucide-react"
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

// Initial article data
const articleUrls = [
  "https://hellosehat.com/lansia/masalah-lansia/penyakit-jantung-pada-lansia/",
  "https://www.klikdokter.com/info-sehat/kesehatan-lansia/kiat-menjaga-kesehatan-jantung-bagi-lansia",
  "https://www.dapurumami.com/artikel-tips/pentingnya-gizi-seimbang-untuk-mencegah-diabetes-pada-lansia/",
]

interface AppUser {
  email: string
  name: string
  role?: string
}

interface ArticleData {
  title: string
  source: string
  url: string
  image: string
}

export default function HomePage(): React.JSX.Element {
  const [currentStatIndex, setCurrentStatIndex] = useState<number>(0)
  const [user, setUser] = useState<AppUser | null>(null)
  const [isLoggedIn, setIsLoggedIn] = useState<boolean>(false)
  const [healthArticles, setHealthArticles] = useState<ArticleData[]>([
    {
      title: "Penyakit Jantung pada Lansia: Gejala, Penyebab, dan Cara Mencegahnya",
      source: "HelloSehat",
      url: "https://hellosehat.com/lansia/masalah-lansia/penyakit-jantung-pada-lansia/",
      image: "https://placehold.co/600x400/2563eb/ffffff?text=Memuat+Gambar...",
    },
    {
      title: "Kiat Menjaga Kesehatan Jantung bagi Lansia",
      source: "KlikDokter",
      url: "https://www.klikdokter.com/info-sehat/kesehatan-lansia/kiat-menjaga-kesehatan-jantung-bagi-lansia",
      image: "https://placehold.co/600x400/2563eb/ffffff?text=Memuat+Gambar...",
    },
    {
      title: "Pentingnya Gizi Seimbang untuk Mencegah Diabetes pada Lansia",
      source: "Dapur Umami",
      url: "https://www.dapurumami.com/artikel-tips/pentingnya-gizi-seimbang-untuk-mencegah-diabetes-pada-lansia/",
      image: "https://placehold.co/600x400/2563eb/ffffff?text=Memuat+Gambar...",
    },
  ])
  const [isLoadingArticles, setIsLoadingArticles] = useState<boolean>(true)

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

  // Fetch article metadata
  useEffect(() => {
    const fetchArticleMetadata = async () => {
      setIsLoadingArticles(true)
      try {
        const updatedArticles = await Promise.all(
          articleUrls.map(async (url) => {
            try {
              const response = await fetch(`/api/fetch-article-metadata?url=${encodeURIComponent(url)}`)

              if (!response.ok) {
                throw new Error(`Failed to fetch metadata for ${url}`)
              }

              const data = await response.json()

              return {
                title: data.title || "Artikel Kesehatan Lansia",
                source: data.source || new URL(url).hostname.replace("www.", ""),
                url: url,
                image:
                  data.image ||
                  `https://placehold.co/600x400/2563eb/ffffff?text=${encodeURIComponent(data.title || "Artikel")}`,
              }
            } catch (error) {
              console.error(`Error fetching metadata for ${url}:`, error)
              // Return fallback data if fetch fails
              return {
                title: "Artikel Kesehatan Lansia",
                source: new URL(url).hostname.replace("www.", ""),
                url: url,
                image: `https://placehold.co/600x400/2563eb/ffffff?text=Artikel+Kesehatan`,
              }
            }
          }),
        )

        setHealthArticles(updatedArticles)
      } catch (error) {
        console.error("Error fetching article metadata:", error)
      } finally {
        setIsLoadingArticles(false)
      }
    }

    fetchArticleMetadata()
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
              <img src="/gambar/logo-new.png" alt="Lansia Care Logo" className="h-12 w-auto" />
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
                        <AvatarImage src="https://placehold.co/32x32/2563eb/ffffff?text=U" alt="User" />
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
                <div></div> /* Empty div to maintain layout */
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
                  <div className="aspect-video bg-gray-200 rounded-t-lg overflow-hidden">
                    <img
                      src={article.image || "/placeholder.svg"}
                      alt={article.title}
                      className="w-full h-full object-cover rounded-t-lg"
                      loading="lazy"
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

      {/* Footer with Contact Information */}
      <footer className="bg-gray-900 text-white py-8">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col items-center justify-center mb-6">
            <div className="flex items-center mb-2">
              <img src="/gambar/logo-new.png" alt="Lansia Care Logo" className="h-10 w-auto mr-3" />
              <span className="text-xl font-semibold">Lansia Care</span>
            </div>
            <p className="text-gray-400 text-center">Platform terpadu layanan kesehatan lansia</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 max-w-4xl mx-auto">
            <div className="flex items-start">
              <MapPinned className="mr-3 h-5 w-5 text-gray-400 flex-shrink-0 mt-1" />
              <p className="text-gray-300 text-sm">
                Jl. Rorojonggrang No.6, Beran, Tridadi, Sleman, Daerah Istimewa Yogyakarta Kode Pos 55511
              </p>
            </div>

            <div className="flex items-center">
              <Phone className="mr-3 h-5 w-5 text-gray-400" />
              <p className="text-gray-300 text-sm">Telp (0274) 868409</p>
            </div>

            <div className="flex items-center">
              <Phone className="mr-3 h-5 w-5 text-gray-400" />
              <p className="text-gray-300 text-sm">Fax (0274) 868409</p>
            </div>

            <div className="flex items-center">
              <Mail className="mr-3 h-5 w-5 text-gray-400" />
              <p className="text-gray-300 text-sm">Email: dinkes@slemankab.go.id</p>
            </div>
          </div>

          <div className="flex items-center justify-center">
            <a
              href="http://dinkes.slemankab.go.id"
              target="_blank"
              rel="noopener noreferrer"
              className="text-blue-300 hover:underline text-sm flex items-center"
            >
              <Globe className="mr-2 h-4 w-4" />
              http://dinkes.slemankab.go.id
            </a>
          </div>

          <div className="border-t border-gray-800 mt-6 pt-6 text-center">
            <p className="text-gray-500 text-sm">&copy; 2024 Lansia Care - Kecamatan Depok, Sleman, DIY</p>
          </div>
        </div>
      </footer>
    </div>
  )
}
