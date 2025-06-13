"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { User, Mail, LogOut, HelpCircle, UserPlus, ArrowLeft } from "lucide-react"
import Link from "next/link"
import { ElderlyDataForm } from "@/components/elderly-data-form"

export default function AccountPage() {
  const [showElderlyForm, setShowElderlyForm] = useState(false)
  const [user] = useState({
    name: "John Doe",
    email: "john.doe@example.com",
    hasElderlyData: false,
  })

  if (showElderlyForm) {
    return <ElderlyDataForm onBack={() => setShowElderlyForm(false)} />
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white">
      <div className="max-w-2xl mx-auto p-4">
        <div className="mb-6">
          <Link href="/">
            <Button variant="ghost" className="mb-4">
              <ArrowLeft className="mr-2 h-4 w-4" />
              Kembali ke Beranda
            </Button>
          </Link>
        </div>

        <Card>
          <CardHeader className="text-center">
            <Avatar className="w-24 h-24 mx-auto mb-4">
              <AvatarImage src="/placeholder.svg?height=96&width=96" alt="User" />
              <AvatarFallback className="text-2xl">JD</AvatarFallback>
            </Avatar>
            <CardTitle className="text-2xl">Akun Saya</CardTitle>
            <CardDescription>Kelola informasi akun Anda</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-4">
              <div className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                <User className="h-5 w-5 text-gray-600" />
                <div>
                  <p className="font-medium">{user.name}</p>
                  <p className="text-sm text-gray-600">Nama Lengkap</p>
                </div>
              </div>

              <div className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                <Mail className="h-5 w-5 text-gray-600" />
                <div>
                  <p className="font-medium">{user.email}</p>
                  <p className="text-sm text-gray-600">Email Terdaftar</p>
                </div>
              </div>
            </div>

            <div className="space-y-3 pt-4">
              <Button className="w-full justify-start" variant="outline" onClick={() => setShowElderlyForm(true)}>
                <UserPlus className="mr-2 h-4 w-4" />
                {user.hasElderlyData ? "Edit Data Lansia" : "Isi Data Lansia"}
              </Button>

              <Button className="w-full justify-start" variant="outline">
                <HelpCircle className="mr-2 h-4 w-4" />
                Bantuan & Dukungan
              </Button>

              <Button className="w-full justify-start" variant="outline">
                <LogOut className="mr-2 h-4 w-4" />
                Keluar
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
