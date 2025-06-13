import { type NextRequest, NextResponse } from "next/server"
import * as cheerio from "cheerio"

export async function GET(request: NextRequest) {
  const url = request.nextUrl.searchParams.get("url")

  if (!url) {
    return NextResponse.json({ error: "URL parameter is required" }, { status: 400 })
  }

  try {
    const response = await fetch(url, {
      headers: {
        "User-Agent":
          "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
      },
    })

    if (!response.ok) {
      return NextResponse.json({ error: "Failed to fetch article" }, { status: response.status })
    }

    const html = await response.text()
    const $ = cheerio.load(html)

    // Try to extract metadata from Open Graph tags
    const title = $('meta[property="og:title"]').attr("content") || $("title").text()
    let image = $('meta[property="og:image"]').attr("content") || $('meta[name="twitter:image"]').attr("content")
    const source = $('meta[property="og:site_name"]').attr("content") || new URL(url).hostname.replace("www.", "")

    // If no image found, try to find the first image in the article
    if (!image) {
      image =
        $("article img").first().attr("src") ||
        $(".article-content img").first().attr("src") ||
        $("main img").first().attr("src") ||
        $(".content img").first().attr("src")
    }

    // If still no image, try to find any image on the page
    if (!image) {
      image = $("img").first().attr("src")
    }

    // Make sure image URL is absolute
    if (image && !image.startsWith("http")) {
      const baseUrl = new URL(url)
      image = image.startsWith("/") ? `${baseUrl.origin}${image}` : `${baseUrl.origin}/${image}`
    }

    // Proxy the image through our API to avoid CORS issues
    if (image) {
      image = `/api/article-proxy?url=${encodeURIComponent(image)}`
    }

    return NextResponse.json({
      title,
      image,
      source,
      url,
    })
  } catch (error) {
    console.error("Error fetching article metadata:", error)
    return NextResponse.json({ error: "Failed to fetch article metadata" }, { status: 500 })
  }
}
