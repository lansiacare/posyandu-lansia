"use client"

import type React from "react"
import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Separator } from "@/components/ui/separator"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import Link from "next/link"

interface FormData {
  name: string
  email: string
  password: string
  confirmPassword: string
  userType: string
  locationId: string
}

interface Location {
  id: string
  name: string
}

export default function RegisterPage(): React.JSX.Element {
  const [formData, setFormData] = useState<FormData>({
    name: "",
    email: "",
    password: "",
    confirmPassword: "",
    userType: "user",
    locationId: "",
  })
  const [isLoading, setIsLoading] = useState<boolean>(false)
  const [error, setError] = useState<string>("")

  const locations: Location[] = [
    { id: "1", name: "Posyandu Condongcatur" },
    { id: "2", name: "Posyandu Caturtunggal" },
    { id: "3", name: "Posyandu Maguwoharjo" },
  ]

  const handleSubmit = (e: React.FormEvent<HTMLFormElement>): void => {
    e.preventDefault()
    setIsLoading(true)
    setError("")

    // Validasi input
    if (!formData.name.trim()) {
      setError("Nama lengkap harus diisi.")
      setIsLoading(false)
      return
    }

    if (!formData.email.trim()) {
      setError("Email harus diisi.")
      setIsLoading(false)
      return
    }

    if (!formData.password) {
      setError("Password harus diisi.")
      setIsLoading(false)
      return
    }

    if (formData.password.length < 6) {
      setError("Password minimal 6 karakter.")
      setIsLoading(false)
      return
    }

    if (formData.password !== formData.confirmPassword) {
      setError("Password dan konfirmasi password tidak cocok.")
      setIsLoading(false)
      return
    }

    if (formData.userType === "kader" && !formData.locationId) {
      setError("Silakan pilih lokasi posyandu untuk akun kader.")
      setIsLoading(false)
      return
    }

    // Simulate registration process
    setTimeout(() => {
      try {
        const user = {
          name: formData.name,
          email: formData.email,
          role: formData.userType,
          ...(formData.userType === "kader" && { locationId: Number.parseInt(formData.locationId, 10) }),
        }

        // Save to localStorage
        localStorage.setItem("user", JSON.stringify(user))

        // Show success message
        alert("Pendaftaran berhasil! Anda akan diarahkan ke halaman utama.")

        // Redirect based on role
        if (formData.userType === "kader") {
          window.location.href = "/kader-dashboard"
        } else {
          window.location.href = "/"
        }
      } catch (err) {
        console.error("Registration error:", err)
        setError("Terjadi kesalahan saat mendaftar. Silakan coba lagi.")
        setIsLoading(false)
      }
    }, 1000)
  }

  const handleGoogleRegister = (type: string): void => {
    setIsLoading(true)

    setTimeout(() => {
      try {
        const user =
          type === "kader"
            ? { name: "Dr. Google Kader", role: "kader", email: "google.kader@example.com", locationId: 1 }
            : { name: "John Google", role: "user", email: "john.google@example.com" }

        localStorage.setItem("user", JSON.stringify(user))

        if (type === "kader") {
          window.location.href = "/kader-dashboard"
        } else {
          window.location.href = "/"
        }
      } catch (err) {
        console.error("Google registration error:", err)
        setError("Terjadi kesalahan saat daftar dengan Google.")
        setIsLoading(false)
      }
    }, 500)
  }

  const handleInputChange = (field: keyof FormData, value: string): void => {
    setFormData((prev) => ({
      ...prev,
      [field]: value,
    }))
  }

  const handleUserTypeChange = (value: string): void => {
    setFormData((prev) => ({
      ...prev,
      userType: value,
      locationId: "", // Reset location when changing user type
    }))
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white flex items-center justify-center p-4">
      <div className="w-full max-w-md">
        <Card>
          <CardHeader className="text-center">
            <div className="w-16 h-16 bg-blue-600 rounded-lg flex items-center justify-center mx-auto mb-4">
              <span className="text-white font-bold text-2xl">LC</span>
            </div>
            <CardTitle className="text-2xl">Daftar Akun Baru</CardTitle>
            <CardDescription>Buat akun untuk mengakses layanan Posyandu Lansia</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            {error && <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{error}</div>}

            <form onSubmit={handleSubmit} className="space-y-4">
              {/* User Type Selection */}
              <div className="space-y-3">
                <Label>Tipe Akun</Label>
                <div className="grid grid-cols-2 gap-4">
                  <label
                    className={`p-3 border rounded-lg cursor-pointer transition-colors ${
                      formData.userType === "user" ? "border-blue-500 bg-blue-50" : "border-gray-300 hover:bg-gray-50"
                    }`}
                  >
                    <input
                      type="radio"
                      name="userType"
                      value="user"
                      checked={formData.userType === "user"}
                      onChange={(e) => handleUserTypeChange(e.target.value)}
                      className="mr-3"
                      disabled={isLoading}
                    />
                    <div>
                      <div className="font-medium">Pengguna Umum</div>
                      <div className="text-xs text-gray-500">Lansia & Keluarga</div>
                    </div>
                  </label>
                  <label
                    className={`p-3 border rounded-lg cursor-pointer transition-colors ${
                      formData.userType === "kader" ? "border-blue-500 bg-blue-50" : "border-gray-300 hover:bg-gray-50"
                    }`}
                  >
                    <input
                      type="radio"
                      name="userType"
                      value="kader"
                      checked={formData.userType === "kader"}
                      onChange={(e) => handleUserTypeChange(e.target.value)}
                      className="mr-3"
                      disabled={isLoading}
                    />
                    <div>
                      <div className="font-medium">Kader Posyandu</div>
                      <div className="text-xs text-gray-500">Petugas Kesehatan</div>
                    </div>
                  </label>
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="name">Nama Lengkap</Label>
                <Input
                  id="name"
                  type="text"
                  placeholder="Masukkan nama lengkap"
                  value={formData.name}
                  onChange={(e) => handleInputChange("name", e.target.value)}
                  required
                  disabled={isLoading}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="email">Email</Label>
                <Input
                  id="email"
                  type="email"
                  placeholder="nama@email.com"
                  value={formData.email}
                  onChange={(e) => handleInputChange("email", e.target.value)}
                  required
                  disabled={isLoading}
                />
              </div>

              {/* Location selection for kader */}
              {formData.userType === "kader" && (
                <div className="space-y-2">
                  <Label htmlFor="location">Lokasi Posyandu</Label>
                  <Select
                    value={formData.locationId}
                    onValueChange={(value) => handleInputChange("locationId", value)}
                    disabled={isLoading}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Pilih Lokasi Posyandu" />
                    </SelectTrigger>
                    <SelectContent>
                      {locations.map((location) => (
                        <SelectItem key={location.id} value={location.id}>
                          {location.name}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              )}

              <div className="space-y-2">
                <Label htmlFor="password">Password</Label>
                <Input
                  id="password"
                  type="password"
                  placeholder="Minimal 6 karakter"
                  value={formData.password}
                  onChange={(e) => handleInputChange("password", e.target.value)}
                  required
                  disabled={isLoading}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="confirmPassword">Konfirmasi Password</Label>
                <Input
                  id="confirmPassword"
                  type="password"
                  placeholder="Masukkan password yang sama"
                  value={formData.confirmPassword}
                  onChange={(e) => handleInputChange("confirmPassword", e.target.value)}
                  required
                  disabled={isLoading}
                />
              </div>

              <Button type="submit" className="w-full" disabled={isLoading}>
                {isLoading ? "Memproses..." : "Daftar"}
              </Button>
            </form>

            <Separator />

            <div className="space-y-2">
              <Button
                type="button"
                variant="outline"
                className="w-full"
                onClick={() => handleGoogleRegister("user")}
                disabled={isLoading}
              >
                <svg className="mr-2 h-4 w-4" viewBox="0 0 24 24">
                  <path
                    fill="currentColor"
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                  />
                  <path
                    fill="currentColor"
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                  />
                  <path
                    fill="currentColor"
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                  />
                  <path
                    fill="currentColor"
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                  />
                </svg>
                Daftar sebagai Pengguna Umum
              </Button>

              <Button
                type="button"
                variant="outline"
                className="w-full"
                onClick={() => handleGoogleRegister("kader")}
                disabled={isLoading}
              >
                <svg className="mr-2 h-4 w-4" viewBox="0 0 24 24">
                  <path
                    fill="currentColor"
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                  />
                  <path
                    fill="currentColor"
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                  />
                  <path
                    fill="currentColor"
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                  />
                  <path
                    fill="currentColor"
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                  />
                </svg>
                Daftar sebagai Kader
              </Button>
            </div>

            <div className="text-center text-sm">
              Sudah punya akun?{" "}
              <Link href="/login" className="text-blue-600 hover:underline">
                Masuk di sini
              </Link>
            </div>
          </CardContent>
        </Card>

        <div className="text-center mt-4">
          <Link href="/" className="text-sm text-gray-600 hover:underline">
            ← Kembali ke Beranda
          </Link>
        </div>
      </div>
    </div>
  )
}
